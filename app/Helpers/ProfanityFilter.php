<?php

namespace App\Helpers;

use Exception;
use Cache;
use Str;
use Arr;

use App\Models\Site\Wordlist\Blacklist;
use App\Models\Site\Wordlist\Whitelist;
use App\Models\Site\Wordlist\LetterSubs;
use App\Models\Site\Wordlist\Context;
use App\Models\Site\Wordlist\ContextBlock;

class ProfanityFilter
{
	// Check for context typing (selling/buying products)
	// Return response depending on $handle_hit value (reject, redact, allow)
		// Submit hit report for review (except phrase/word boundary blacklisted ones cuz those shouldn't catch strays anyways)


	public static $populated;
	public static $has_hits;
	public static $hit_count;
	public static $hit_words;
	public static $hit_contexts;
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
		// Run context checks
		self::checkContexts($content);


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
		if($boundary && preg_match_all($boundary, $content, $matches['black']['boundary'])) {
			$content = preg_replace($boundary, '*****', $content, -1);
		}
		if($substring && preg_match_all($substring, $content, $matches['black']['substring'])) {
			$content = preg_replace($substring, '*****', $content, -1);
		}
		self::$hit_words = Arr::flatten($matches['black']);
		self::$hit_count = count(self::$hit_words) + count(self::$hit_contexts);
		if(self::$hit_count) self::$has_hits ?? true;


		// Return whitelisted words to their original place
		if($matches['white'][0] != []) {
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

			// sub-words get [^\s\[\]]* before and after -- this collects surrounding letters, but breaks w/ ] or [ for bbcode
			$bounds = $type == 'substring' ? '[^\s\[\]]*' : '';

			return "/\b{$bounds}(?:".implode('|', $words)."){$bounds}\b/i";
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

	private static function checkContexts($content): void {
		// Prep the content with the context tags
		$contexts = self::getContexts();
		$content = preg_replace($contexts['words'], $contexts['keys'], str_replace('<', '&lt;', $content));

		// Build the blocklist for contexts here
		$tags = ContextBlock::pluck('contexts')->toArray();
		$nicknames = ContextBlock::pluck('nickname')->toArray();

		// It needs to have at least one of EACH to count as the context block
		$hits = [];
		foreach($tags as $key => $tag) {
			if(Str::containsAll($content, json_decode($tag))) array_push($hits, $nicknames[$key]);
		}

		if(!isset(self::$hit_contexts)) self::$hit_contexts = $hits;

		return;
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