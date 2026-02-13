<?php

namespace App\Helpers;

use Exception;
use Cache;
use Str;
use Arr;

use App\Models\Site\Wordlist\FilterList;
use App\Models\Site\Wordlist\LetterSubs;
use App\Models\Site\Wordlist\Contexts;
use App\Models\Site\Wordlist\ContextBlocks;

class ProfanityFilter
{
	public static $populated;
	public static $report;
	public static $prevent;
	public static $stashed_hits;
	public static $filtered_content;

	// This is the primary use of this Helper
	public static function filter(string $content): void {
		if(!isset(self::$populated)) {
			self::$populated = true;
			self::runChecks($content);
		}
		return;
	}


	// Private functions as these shouldn't be called anywhere but inside here (i.e. filter function)
	private static function runChecks(string $content): void {
		// Run context checks before anything gets changed
		self::checkContexts($content);


		// Strip whitelisted words
		$saved_words = [];
		$whitelist = self::getWhitelist();
		// Safeword is like this to make it VERY hard for someone to accidentally produce a "fake" whitelist spot and mess up their returned content
		$safeword = preg_quote(' $4f3'.time().'W0Rd ');
		if($whitelist && preg_match_all($whitelist, $content, $saved_words)) {
			$content = preg_replace($whitelist, $safeword, $content);
		}


		$stashed_hits = [];
		if($boundary = self::getBlacklist('boundary')) {
			$checked = self::checkForHits($content, $boundary);
			if($checked[1]) $stashed_hits[] = $checked[1];
			$content = $checked[0];
		}

		if($substring = self::getBlacklist('substring')) {
			$checked = self::checkForHits($content, $substring, true);
			if($checked[1]) $stashed_hits[] = $checked[1];
			$content = $checked[0];
		}
		self::$stashed_hits = Arr::join($stashed_hits, ', '); // These are being sent through the word report.


		// Return whitelisted words to their original place
		if(($whites = $saved_words[0]) != []) {
			foreach($whites as $match) {
				$content = preg_replace("/{$safeword}/", $match, $content, 1);
			}
		}

		// We've now filtered the main content, so stash it
		self::$filtered_content = $content;


		return;
	}



	// Helper functions for this file.
	private static function checkForHits(string $content, array $lists, bool $stash = false): array {
		$hit_strings = [];
		foreach($lists as $hit_level => $regex) {
			if(preg_match_all($regex, $content, $hits)) {
				if($stash) {
					if($hits[0] != []) $hit_strings[$hit_level] = Arr::join($hits[0], ', ');
				}
				if($hit_level & 1) $content = preg_replace($regex, '*****', $content, -1);
				if($hit_level & 2 && !isset(self::$report)) self::$report = true;
				if($hit_level & 4 && !isset(self::$prevent)) self::$prevent = true;
			}
		}
		if($hit_strings == []) $hit_strings = null;
		else $hit_strings = Arr::join($hit_strings, ', ');
		return [$content, $hit_strings];
	}


	private static function getBlacklist(string $type): array {
		return Cache::tags(['profanity'])->rememberForever("{$type}", function () use ($type) {
			$words = FilterList::where('filter_type', '=', $type)->select(['regex', 'handle_hit'])->get()->groupBy('handle_hit')->toArray();
			if($words == []) return null;

			// sub-words get [^\s\[\]]* before and after -- this collects surrounding letters, but breaks w/ ] or [ for bbcode
			$bounds = $type == 'substring' ? '[^\s\[\]]*' : '';

			foreach($words as $level => $data) {
				$regexes = Arr::pluck($data, 'regex');
				$words[$level] = "/\b{$bounds}(?:".implode('|', $regexes)."){$bounds}\b/i";
			}

			return $words;
		});
	}

	private static function getWhitelist(): string {
		// Timing: min 86400 (1 day) max 172800 (2 days)
		// return Cache::flexible('blacklist_'.$type, [86400, 172800], function () {
		return Cache::flexible('whitelist', [10, 30], function () {
			$words = FilterList::where('filter_type', '=', 'whitelist')->pluck('regex')->toArray();
			if($words == []) return null;

			// Whitelisted words should always be as boundary-words.
			// Otherwise users could find whitelisted words to "protect" their blacklisted word usage.
			$regex = '/\b(?:'.implode('|', $words).')\b/i';

			return $regex;
		});
	}

	private static function checkContexts(string $content): void {
		// Prep the content with the context tags
		$contexts = Cache::tags(['profanity'])->rememberForever("context", function () {
			return Contexts::get()->toArray();
		});
		$content = preg_replace(Arr::pluck($contexts, 'regex'), Arr::pluck($contexts, 'context'), str_replace('<', '&lt;', $content));

		// Build the blocklist for contexts here
		$blocks = ContextBlocks::select(['nickname', 'contexts', 'handle_hit'])->get()->toArray();

		foreach($blocks as $block) {
			$tags = json_decode($block['contexts']);
			$hit_level = $block['handle_hit'];
			if(Str::containsAll($content, $tags)) {
				// if($hit_level & 1) // Trying to filter out context hits is going to be kind of difficult, so come back to this. Reporting/Preventing are the big ones atm.
				if($hit_level & 2 && !isset(self::$report)) self::$report = true;
				if($hit_level & 4 && !isset(self::$prevent)) self::$prevent = true;
			}
		}

		return;
	}



	// Helper functions for adding/updating the lists in the DB, to be used through Services.
	public static function regexifyWords(array $words): array {
		$letters = LetterSubs::pluck('letter')->toArray();
		$subs = LetterSubs::pluck('regex')->toArray();

		$subbed = preg_replace($letters, $subs, $words);

		return $subbed;
	}

	public static function regexifySubs(array $subs): string {
		return '['.preg_quote(implode('', $subs)).']+[ _-]*?';
	}
}