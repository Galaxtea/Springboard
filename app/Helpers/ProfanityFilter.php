<?php

namespace App\Helpers;

use Exception;
use Cache;

use App\Models\Site\Wordlist\Blacklist;
use App\Models\Site\Wordlist\Whitelist;
use App\Models\Site\Wordlist\LetterSubs;
use App\Models\Site\Wordlist\Context;
use App\Models\Site\Wordlist\ContextBlock;
use App\Models\Site\Wordlist\WordReport;

class ProfanityFilter
{
	// Check for context typing (selling/buying products)
	// Return response depending on $handle_hit value (reject, redact, allow)
		// Submit hit report for review (except phrase/word boundary blacklisted ones cuz those shouldn't catch strays anyways)


	public static $populated;
	public static $has_hits;
	public static $hit_count;
	public static $hit_words;
	public static $hit_context;
	public static $filtered_content;

	// This is the primary use of this Helper
	public static function filter(string $content): null|bool {
		if(!isset(self::$populated)) {
			self::$populated = true;
			self::runChecks($content);
		}
		return self::$has_hits;
	}


	// Private functions as these shouldn't be called anywhere but inside here (i.e. filter function)
	private static function runChecks(string $content): void {
		// We want to check for blocked contexts first because it'll be a hard reject
		// Remember that $hit_context will be populated to check on the other side
		if(self::checkContexts($check_context)) return;


		// We didn't hit any blocked contexts, so follow up with the black/whitelists
		// Get cached blacklists & whitelist. Initiate arrays for matches.
		$matches = ['white'=>[], 'black'=>[]];
		$boundary = self::getBlacklist('boundary');
		$substring = self::getBlacklist('substring');
		$whitelist = self::getWhitelist();


		// Strip whitelisted words
		// Safeword is like this to make it VERY hard for someone to accidentally produce a "fake" whitelist spot and mess up their returned content
		$safeword = preg_quote('$4f3'.time().'W0Rd');
		if($whitelist && preg_match_all($whitelist, $content, $matches['white'])) {
			$content = preg_replace($whitelist, $safeword, $content);
		}


		// Check for blacklisted content, make sure to do boundary-word checks first
		$bounding_hits = 0;
		if($boundary && preg_match_all($boundary, $content, $matches['black']['boundary'])) {
			$content = preg_replace($boundary, '*****', $content, -1, $bounding_hits);
			if(!isset(self::$has_hits)) self::$has_hits = true;
		}
		$substring_hits = 0;
		if($substring && preg_match_all($substring, $content, $matches['black']['substring'])) {
			$content = preg_replace($substring, '*****', $content, -1, $substring_hits);
			if(!isset(self::$has_hits)) self::$has_hits = true;
		}
		self::$hit_words = $matches['black'];
		self::$hit_count = $bounding_hits + $substring_hits;


		// Return whitelisted words to their original place
		if(!$matches['white'][0] == []) {
			foreach($matches['white'][0] as $match) {
				$content = preg_replace("/{$safeword}/", $match, $content, 1);
			}
		}


		// We've now filtered the main content, so stash it
		self::$filtered_content = $content;


		return;
	}


	// Helper functions for this file.
	private static function getBlacklist(string $type): string {
		// Timing: min 86400 (1 day) max 172800 (2 days)
		// return Cache::flexible('blacklist_'.$type, [86400, 172800], function () {
		return Cache::flexible('blacklist_'.$type, [10, 30], function () use ($type) {
			$words = Blacklist::where('filter_type', '=', $type)->pluck('subbed')->toArray();
			if($words == []) return null;


			// sub-words get \S* before and after
			$bounds = $type == 'substring' ? '\S*' : '';
			$endings = $type == 'substring' ? '' : '(?:'.implode('|', Blacklist::where('filter_type', '=', 'ending')->pluck('subbed')->toArray()).')*';


			$regex = "/\b{$bounds}(?:".implode('|', $words)."){$endings}{$bounds}\b/i";
			// Also save a list of boundary words but as substring words? So we can get alerts about them and can pre-emptively fill whitelists, and also see if it's something we need to alter the blacklist for

			return $regex;
		});
	}

	private static function getWhitelist(): string {
		// Timing: min 86400 (1 day) max 172800 (2 days)
		// return Cache::flexible('blacklist_'.$type, [86400, 172800], function () {
		return Cache::flexible('whitelist', [10, 30], function () {
			$words = Whitelist::pluck('word')->toArray();
			if($words == []) return null;

			$endings = '(?:'.implode('|', Blacklist::where('filter_type', '=', 'ending')->pluck('subbed')->toArray()).')*';

			// Whitelisted words should always be as boundary-words.
			// Otherwise users could find whitelisted words to "protect" their blacklisted word usage.
			$regex = '/\b(?:'.implode('|', $words).')'.$endings.'\b/i';

			return $regex;
		});
	}

	private static function checkContexts($content): bool {
		// Prep the content with the context tags
		$contexts = self::getContexts();
		$content = preg_replace($contexts['words'], $contexts['keys'], str_replace('<', '&lt;', $content));

		// Build the blocklist for contexts here
		$tags = ContextBlock::pluck('contexts')->toArray();
		$nicknames = ContextBlock::pluck('nickname')->toArray();

		// It needs to have at least one of EACH to count as the context block
		foreach($tags as $key => $tag) {
			if(self::str_contains_all($content, json_decode($tag))) {
				self::$hit_context = $nicknames[$key];
				self::$has_hits = true;
				self::$hit_count = 1;
				return true; // We hit a blocked context, so no need to keep searching
			}
		}
		return false;
	}

	private static function getContexts(): array {
		$contexts = Context::get()->toArray();
		$return = ['words'=>[], 'keys'=>[]];
		foreach($contexts as $data) {
			array_push($return['words'], $data['subbed']);
			array_push($return['keys'], $data['context']);
		}
		return $return;
	}

	// ---- This function came from the PHP str_contains() doc page
	private static function str_contains_all(string $haystack, array $needles): bool {
	    return array_reduce($needles, fn($a, $n) => $a && str_contains($haystack, $n), true);
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