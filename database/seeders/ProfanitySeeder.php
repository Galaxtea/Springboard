<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Arr;

use App\Models\Site\Wordlist\FilterList;
use App\Models\Site\Wordlist\LetterSubs;
use App\Models\Site\Wordlist\Contexts;
use App\Models\Site\Wordlist\ContextBlocks;

use App\Helpers\ProfanityFilter as Filter;

class ProfanitySeeder extends Seeder
{
	// If you have any regular letters in the sub list, they NEED to be capitalized. i.e. Z, not z, as a sub for s
	// If you do lowercase instead, it breaks regex-prepping the full words.
	private $letter_subs = [
		'/a/' => ['a', '4', '@', 'á', 'à', 'â', 'ä', 'ã', 'å', 'æ', 'α', 'Δ', 'λ'],
		'/b/' => ['b', '8', '3', 'ß', 'β'],
		'/c/' => ['c', 'K', 'S', 'ç', 'ć', 'č', '¢', '€', '<', '(', '{', '©'],
		'/d/' => ['d', ')', 'þ', 'ð'],
		'/e/' => ['e', '3', '€', 'è', 'é', 'ê', 'ë', 'ē', 'ė', 'ę', '∑'],
		'/f/' => ['f', 'ƒ'],
		'/g/' => ['g', 'J', '6', '9'],
		'/h/' => ['h', 'Η'],
		'/i/' => ['i', 'L', '!', '|', ']', '[', '1', '∫', 'ì', 'í', 'î', 'ï', 'ī', 'į'],
		'/j/' => ['j', 'G'],
		'/k/' => ['k', 'κ'],
		'/l/' => ['l', 'I', '!', '|', ']', '[', '1', '£', '∫', 'ì', 'í', 'î', 'ï', 'ī', 'į', 'Ł'],
		'/m/' => ['m'],
		'/n/' => ['n', 'M', 'η', 'Ν', 'Π', 'ñ', 'ń'],
		'/o/' => ['o', '0', 'ο', 'Φ', '¤', '°', 'ø', 'ô', 'ö', 'ò', 'ó', 'œ', 'ō', 'õ'],
		'/p/' => ['p', 'ρ', 'Ρ', '¶', 'þ'],
		'/q/' => ['q'],
		'/r/' => ['r', '®'],
		'/s/' => ['s', 'Z', 'C', '5', '$', '§', 'ß', 'ś', 'š'],
		'/t/' => ['t', '7', 'τ'],
		'/u/' => ['u', 'V', 'W', 'Y', 'υ', 'µ', 'û', 'ü', 'ù', 'ú', 'ū', '@', '*'],
		'/v/' => ['v', 'Y', 'U', 'υ', 'ν'],
		'/w/' => ['w', 'U', 'ω', 'Ψ'],
		'/x/' => ['x', 'χ'],
		'/y/' => ['y', 'I', 'V', '¥', 'γ', 'ÿ', 'ý'],
		'/z/' => ['z', 'S', 'Ζ', 'ž', 'ź', 'ż'],
	];


	// Use word roots for the blacklisted substring words.
	private $blacklist = [
		// The site's self-reporting feature will help populate this with additional words confirmed for the blacklist.
		// Fill this with full words that should get hit.
		'boundary' => [
			['ass', ['es'], 0],
			['asshole', ['s'], 0],
			['hell', [], 0],
			['cock', ['s'], 0],
			['jap', ['s'], 1],
			['rape', ['d', 'r', 's'], 1],
			['rapist', ['s'], 1],
			['raping', ['s'], 1],
			['orgy', ['s'], 1],
			['orgies', [], 1],
			['hitler', [], 3],
			['nazi', ['s'], 3],
			['swastika', ['s'], 3],
			['nigga', ['s'], 1],
			['nigger', ['s'], 1],
			['faggot', ['s'], 1],
			['fag', ['s'], 1],
			['boob', ['s', 'ie'], 0],
			['fuck', ['s', 'ed', 'er', 'ing'], 0],
			['shit', ['s', 'ting', 'ter'], 0],
			['damn', ['ed'], 0],
			['dammit', ['s'], 0],
			['bitch', ['ed', 'es'], 0],
			['abortion', ['ed', 's', 'ist', 'ing'], 3],
		],

		// Fill this list with word roots to check after the full-word list is checked
		'substring' => [
			['ass', [], 0],
			['asshole', [], 0],
			['hell', [], 0],
			['cock', [], 0],
			['jap', [], 0],
			['rapist', [], 1],
			['orgy', [], 0],
			['orgie', [], 0],
			['hitler', [], 3],
			['nazi', [], 3],
			['swastika', [], 3],
			['twat', [], 0],
			['rape', [], 3],
			['boob', [], 0],
			['fak', [], 0],
			['fuk', [], 0],
			['fuck', [], 0],
			['shit', [], 0],
			['damn', [], 0],
			['bitch', [], 0],
			['biatch', [], 0],
			['abortion', [], 3],
			['arse', [], 0],
			['ahole', [], 0],
			['porn', [], 3],
			['cunt', [], 1],
			['hore', [], 1],
			['hoor', [], 1],
			['nig', [], 1],
			['fag', [], 1],
			['cum', [], 0],
			['semen', [], 0],
			['slut', [], 3],
			['tard', [], 3],
			['anal', [], 0],
			['penis', [], 0],
			['boner', [], 0],
			['clit', [], 0],
			['vag', [], 0],
			['dick', [], 0],
			['dyke', [], 3],
			['abuse', [], 3],
			['axwound', [], 3],
			['axewound', [], 3],
			['blowjob', [], 3],
			['gangbang', [], 3],
			['dildo', [], 0],
			['molest', [], 3],
			['pedo', [], 3],
			['smut', [], 3],
			['masterba', [], 3],
			['masturba', [], 3],
			['orgas', [], 3],
			['wog', [], 1],
			['ejac', [], 3],
			['horny', [], 1],
			['horne', [], 1],
		],
	];


	// The site's self-reporting feature will help populate this with additional words confirmed for the whitelist.
	// Fill this with full words that shouldn't get hit.
	private $whitelist = [
		['cockpit', ['s'], 0],
		['grape', ['s'], 0],
		['hornet', ['s'], 0],
		['basement', ['s'], 0],
		['canal', ['s'], 0],
		['analyst', ['s'], 0],
		['analysis', ['es'], 0],
		['analyze', ['s', 'd'], 0],
		['skuntank', ['s'], 0],
		['parse', ['d', 'r', 's'], 0],
		['vacuum', ['s', 'ed', 'ing'], 0],
		['scunthorpe', [], 0],
		['penistone', [], 0],
		['arsenal', ['s'], 0],
		['cocktail', ['s'], 0],
		['cockerel', ['s'], 0],
		['cockatiel', ['s'], 0],
		['cockscomb', ['s'], 0],
	];


	private $word_context = [
		'<sales>' => ['sale', 'sell', 'sold'],
		'<trades>' => ['trade', 'trading'],
		'<site content>' => ['account', 'adopt', 'pet', 'item', 'inventory', 'treasure'],
		'<rlc>' => ['dollar', 'cent', 'money', 'USD', 'GBP', 'pound', 'pence', 'euro', 'yen', '\$', '¢', '€', '£', '¥'],
		'<offsite>' => ['offsite', 'facebook', 'flightrising', 'dragcave', 'dragoncave', 'FB', 'FR', 'FV', 'furvilla', 'DC', 'chickensmoothie', 'CS'],
	];
	private $context_blocks = [
		'Offsite Sales' => [1, ['<sales>', '<site content>', '<offsite>']],
		'RLC Sales' => [3, ['<sales>', '<site content>', '<rlc>']],
		'Offsite Trades' => [1, ['<trades>', '<site content>', '<offsite>']],
		'RLC Trades' => [3, ['<trades>', '<site content>', '<rlc>']],
	];



	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		// Remove existing data for when we're re-seeding an existing DB
		FilterList::truncate();
		LetterSubs::truncate();
		Contexts::truncate();
		ContextBlocks::truncate();
		cache()->flush();


		// Letter Subs
		// We run this one first so we can use it for the FilterList words.
		foreach($this->letter_subs as $letter => $sub) {
			$sub_list = json_encode($sub);
			$regex = Filter::regexifySubs($sub);
			LetterSubs::create(['letter' => $letter, 'subs' => $sub_list, 'regex' => $regex]);
		}


		// Whitelist
		foreach($this->whitelist as $word_data) {
			// $word_data = ['cockatiel', ['s'], 0];
			if($word_data[1] == []) $endings = '';
			else $endings = '(?:'.implode('|', $word_data[1]).')*';

			$regex = "(?:{$word_data[0]}{$endings})";

			FilterList::create(['word' => $word_data[0], 'filter_type' => 'whitelist', 'regex' => $regex, 'handle_hit' => 0, 'endings' => json_encode($word_data[1])]);
		}
		// Blacklists
		foreach($this->blacklist as $type => $words) {
			$subbed = Filter::regexifyWords(Arr::pluck($words, 0));

			foreach($words as $key => $data) {
				if($data[1] == []) $endings = '';
				else $endings = '(?:'.implode('|', Filter::regexifyWords($data[1])).')*';

				$regex = "(?:{$subbed[$key]}{$endings})";

				FilterList::create(['word' => $data[0], 'filter_type' => $type, 'regex' => $regex, 'handle_hit' => $data[2], 'endings' => json_encode($data[1])]);
			}
		}


		// Contexts
		foreach($this->word_context as $context => $words) {
			$regex = '/(?:'.implode('|', Filter::regexifyWords($words)).')/i';

			Contexts::create(['context' => $context, 'words' => json_encode($words), 'regex' => $regex]);
		}
		// Context Blocks
		foreach($this->context_blocks as $nickname => $data) {
			ContextBlocks::create(['nickname' => $nickname, 'contexts' => json_encode($data[1]), 'handle_hit' => $data[0]]);
		}
	}
}
