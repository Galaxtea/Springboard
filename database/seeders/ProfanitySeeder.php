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
			['ass', ['es'], 1],
			['asshole', ['s'], 1],
			['hell', [], 1],
			['cock', ['s'], 1],
			['jap', ['s'], 3],
			['rape', ['d', 'r', 's'], 3],
			['rapist', ['s'], 3],
			['raping', ['s'], 3],
			['orgy', ['s'], 3],
			['orgies', [], 3],
			['hitler', [], 6],
			['nazi', ['s'], 6],
			['swastika', ['s'], 6],
			['nigga', ['s'], 3],
			['nigger', ['s'], 3],
			['faggot', ['s'], 3],
			['fag', ['s'], 3],
			['boob', ['s', 'ie'], 1],
			['fuck', ['s', 'ed', 'er', 'ing'], 1],
			['shit', ['s', 'ting', 'ter'], 1],
			['damn', ['ed'], 1],
			['dammit', ['s'], 1],
			['bitch', ['ed', 'es'], 1],
			['abortion', ['ed', 's', 'ist', 'ing'], 6],
		],

		// Fill this list with word roots to check after the full-word list is checked
		'substring' => [
			['ass', [], 1],
			['asshole', [], 1],
			['hell', [], 1],
			['cock', [], 1],
			['jap', [], 1],
			['rapist', [], 3],
			['orgy', [], 1],
			['orgie', [], 1],
			['hitler', [], 6],
			['nazi', [], 6],
			['swastika', [], 6],
			['twat', [], 1],
			['rape', [], 6],
			['boob', [], 1],
			['fak', [], 1],
			['fuk', [], 1],
			['fuck', [], 1],
			['shit', [], 1],
			['damn', [], 1],
			['bitch', [], 1],
			['biatch', [], 1],
			['abortion', [], 6],
			['arse', [], 1],
			['ahole', [], 1],
			['porn', [], 6],
			['cunt', [], 3],
			['hore', [], 3],
			['hoor', [], 3],
			['nig', [], 3],
			['fag', [], 3],
			['cum', [], 1],
			['semen', [], 1],
			['slut', [], 6],
			['tard', [], 6],
			['anal', [], 1],
			['penis', [], 1],
			['boner', [], 1],
			['clit', [], 1],
			['vag', [], 1],
			['dick', [], 1],
			['dyke', [], 6],
			['abuse', [], 6],
			['axwound', [], 6],
			['axewound', [], 6],
			['blowjob', [], 6],
			['gangbang', [], 6],
			['dildo', [], 1],
			['molest', [], 6],
			['pedo', [], 6],
			['smut', [], 6],
			['masterba', [], 6],
			['masturba', [], 6],
			['orgas', [], 6],
			['wog', [], 3],
			['ejac', [], 6],
			['horny', [], 3],
			['horne', [], 3],
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
		'Offsite Sales' => [2, ['<sales>', '<site content>', '<offsite>']],
		'RLC Sales' => [6, ['<sales>', '<site content>', '<rlc>']],
		'Offsite Trades' => [2, ['<trades>', '<site content>', '<offsite>']],
		'RLC Trades' => [6, ['<trades>', '<site content>', '<rlc>']],
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
		\Cache::flush();


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
