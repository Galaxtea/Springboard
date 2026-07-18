<?php

namespace App\Helpers;

use Exception;
use Arr;
use Str;
use DB;

use App\Models\Site\Wordlist\Contexts;
use App\Models\Site\Wordlist\ContextBlocks;
use App\Models\Site\Wordlist\LetterSubs;
use App\Models\Site\Wordlist\FilterList;

class ProfanityFilter
{
	// WIP: 0010, 0100
	/* Blacklist & Context filter flags:
		* 0001 - & 1 - Redact (not currently available for Context filtering)
		* 0010 - & 2 - Report content for review
		* 0100 - & 4 - Hold content for review
		* 1000 - & 8 - Refuse content (respects 0010: will send a report about the user specifically with what they tried to post, and refuse their actual post)
	*/

	private static $populated;
	public static $filtered;
	public static $flags;
	private static $saved_words;

	public static function filter(string $input): void {
cache()->tags(['profanity'])->flush(); // THIS LINE IS TEMPORARY and should be removed once the admin panel is available for editing the word lists/filters.
		if(!isset(self::$populated)) {
			self::$populated = true;

			$input = str_replace('<', '&lt;', $input);
			self::runFilters($input);
		}
	}





	private static function runFilters(string $input): void {
		$flags = 0;

		// check contexts
		[$_flags, $output] = self::filterContexts($input);
			$flags = $flags | $_flags;
		// extract whitelisted words
			$output = self::filterWhitelist($output);
		// handle blacklist (boundary) words
		[$_flags, $output] = self::filterBlacklist($output, 'boundary');
			$flags = $flags | $_flags;
		// handle blacklist (substring) words
		[$_flags, $output] = self::filterBlacklist($output, 'substring');
			$flags = $flags | $_flags;
		// add back extracted whitelisted words
			$output = self::filterWhitelist($output, 'replace');

		self::$flags = $flags;
		self::$filtered = $output;
	}




	private static function filterContexts(string $input): array {
		$flags = 0;

		// Eventually pull out these rememberForevers and put them into a cache warmer instead
			$contexts = cache()->tags(['profanity'])->rememberForever("contexts", function() {
				return Contexts::select(['context', 'regex'])->get()->toArray();
			});
			$blocks = cache()->tags(['profanity'])->rememberForever("context_blocks", function() {
				return ContextBlocks::select(['nickname', 'contexts', 'handle_hit'])->get()->toArray();
			});

		// Swap in the context keywords for use by the context blocks
		$filtered = $input;
		$input = preg_replace(Arr::pluck($contexts, 'regex'), Arr::pluck($contexts, 'context'), $input);

		foreach($blocks as $block) {
			$keywords = json_decode($block['contexts']);
			$_flags = $block['handle_hit'];
			if(Str::containsAll($input, $keywords)) {
				$flags = $flags | $_flags;
				if($_flags & 1) {
					/* This is where we would filter the input if desired.
						To do this, we'll need to take the original input (i.e. we need a second variable for the content)
						and then do preg_replace with this block's contexts, and update the input for return */
				}
			}
		}

		return [$flags, $filtered];
	}

	private static function filterWhitelist(string $input, string $method = 'extract'): string {
		if($method == 'extract') {
			// Send to the cache warmer :)
				$whitelist = cache()->tags(['profanity'])->rememberForever("whitelist", function() {
					$words = FilterList::select(['regex'])->where('filter_type', 'whitelist')->pluck('regex')->toArray();
					if($words == []) return null;

					// Whitelisted words should always be as boundary-words.
					// Otherwise users could find whitelisted words to "protect" their blacklisted word usage.
					$regex = '/\b(?:'.implode('|', $words).')\b/i';

					return $regex;
				});

			// we have the whitelist so now do the thing
			if($whitelist && preg_match_all($whitelist, $input, $saved_words)) {
				self::$saved_words = $saved_words[0];
				$input = preg_replace($whitelist, "<safeword>", $input);
			}
		} elseif($method == 'replace') {
			if(($words = self::$saved_words) != []) {
				foreach($words as $word) {
					$input = preg_replace("/<safeword>/", $word, $input, 1);
				}
			}
		}
		return $input;
	}

	private static function filterBlacklist(string $input, string $type = 'boundary'): array {
		$flags = 0;
		if($type == 'boundary' || $type == 'substring') {
			// Cache warming pls -- build up the blacklists as flag groups to simplifying updating the flag list
				$blacklist = cache()->tags(['profanity'])->rememberForever("blacklist:{$type}", function() use ($type) {
					$words = FilterList::select(['regex', 'handle_hit'])->where('filter_type', $type)->get()->groupBy('handle_hit')->toArray();
					if($words == []) return null;

					// sub-words get [^\s\[\]]* before and after -- this collects surrounding letters, but breaks w/ ] or [ for bbcode
					$bounds = $type == 'substring' ? '[^\s\[\]]*' : '';

					foreach($words as $_flags => $list) {
						$regexes = Arr::pluck($list, 'regex');
						$words[$_flags] = "/\b{$bounds}(?:".implode('|', $regexes)."){$bounds}\b/i";
					}
					return $words;
				});

			// Now we filter the text
			$hits = [];
			foreach($blacklist as $_flags => $regex) {
				if(preg_match_all($regex, $input, $_hits)) {
					$input = preg_replace($regex, '*****', $input, -1);
					$flags = $flags | $_flags;
					// we need to append these $_hits to the $hits so they can be used below
					$hits[] = $_hits[0];
				}
			}

			if($type == 'substring') {
				// We'll want the substring hits to be added to the FilterList as 'pending' type so they can be reviewed
				// This is something that should be sent to the queue to be handled in the future, but will be fine here for now.
				DB::beginTransaction();
					foreach($hits as $sub_hit) {
						$regexes = self::regexifyWords($sub_hit);
						foreach($sub_hit as $key => $hit) {
							FilterList::insertOrIgnore([
								'word' => $hit,
								'filter_type' => 'pending',
								'regex' => "(?:{$regexes[$key]})",
								'endings' => "[]",
							]);
						}
					}
				DB::commit();
			}
		}
		return [$flags, $input];
	}






	// Helper functions for adding/updating the lists in the DB, to be used through Services.
	public static function regexifyWords(array $words): array {
		$sub_list = LetterSubs::select(['letter', 'regex'])->get()->toArray();
		[$letters, $subs] = [Arr::pluck($sub_list, 'letter'), Arr::pluck($sub_list, 'regex')];

		$subbed = preg_replace($letters, $subs, $words);

		return $subbed;
	}

	public static function regexifySubs(array $subs): string {
		return '['.preg_quote(implode('', $subs)).']+[ _-]*?';
	}
}