<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


use App\Models\Site\Wordlist\Blacklist;
use App\Models\Site\Wordlist\Whitelist;
use App\Models\Site\Wordlist\LetterSubs;
use App\Models\Site\Wordlist\Context;
use App\Models\Site\Wordlist\ContextBlock;

use App\Helpers\ProfanityFilter as Filter;

class ProfanitySeeder extends Seeder
{
	// If you have any regular letters in the sub list, they NEED to be capitalized. i.e. Z, not z, as a sub for s
	// If you do lowercase instead, it breaks regex-prepping the full words.
	private $letter_subs = [
		'/a/' => ['a', 'E', '4', '@', 'á', 'à', 'â', 'ä', 'ã', 'å', 'æ', 'α', 'Δ', 'λ'],
		'/b/' => ['b', '8', '3', 'ß', 'β'],
		'/c/' => ['c', 'K', 'S', 'ç', 'ć', 'č', '¢', '€', '<', '(', '{', '©'],
		'/d/' => ['d', ')', 'þ', 'ð'],
		'/e/' => ['e', 'U', '3', '€', 'è', 'é', 'ê', 'ë', 'ē', 'ė', 'ę', '∑'],
		'/f/' => ['f', 'ƒ'],
		'/g/' => ['g', 'J', '6', '9'],
		'/h/' => ['h', 'Η'],
		'/i/' => ['i', 'L', '!', '|', ']', '[', '1', '∫', 'ì', 'í', 'î', 'ï', 'ī', 'į'],
		'/j/' => ['j', 'G'],
		'/k/' => ['k', 'κ'],
		'/l/' => ['l', 'I', '!', '|', ']', '[', '1', '£', '∫', 'ì', 'í', 'î', 'ï', 'ī', 'į', 'Ł'],
		'/m/' => ['m', 'N'],
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


	// Use word roots for the blacklisted words. Your endings should cover for most variants.
	private $blacklist = [
		'boundary' => [ // Use this for words that end up sub-words of a lot of legit words (i.e. "ass")
			'ass',
			'asshole',
			'hell',
			'cock',
			'jap',
			'rapist',
			'orgy',
			'orgie',
		],
		'substring' => [ // Use this for words that are unlikely to hit legit words. This should be your most common list to fill.
			'hitler',
			'nazi',
			'swastika',
			'twat',
			'rape',
			'boob',
			'fak',
			'fuk',
			'fuck',
			'shit',
			'damn',
			'bitch',
			'biatch',
			'abortion',
			'arse',
			'ahole',
			'porn',
			'cunt',
			'hore',
			'hoor',
			'nig',
			'fag',
			'cum',
			'semen',
			'slut',
			'tard',
			'anal',
			'penis',
			'boner',
			'clit',
			'vag',
			'dick',
			'dyke',
			'abuse',
			'axwound',
			'axewound',
			'blowjob',
			'gangbang',
			'dildo',
			'molest',
			'pado',
			'smut',
			'masterba',
			'orgas',
			'wog',
			'ejac',
			'horny',
			'horne',
		],
		'ending' => [ // These can combo on themselves, so having "s" and "ism" already covers "isms" for example
			'r', 'd', 'e', 's', 't', 'y', 'er', 'es', 'ed', 'ist', 'ies', 'ing', 'ism', 'oid'
		],
	];
	private $whitelist = [
		'cockpit', 'grape', 'eject', 'hornet', 'veggie', 'vegetables', 'basement', 'canal', 'analyst', 'analysis', 'analyze', 'skuntank', 'parse', 'vacuum', 'scunthorpe', 'penistone', 'arsenal', 'cocktail', 'cockerel', 'cockatiel'
	];


	private $word_context = [
		'<sales>' => ['sale', 'sell', 'sold'],
		'<trades>' => ['trade', 'trading'],
		'<site content>' => ['account', 'adopt', 'pet', 'item', 'inventory', 'treasure'],
		'<rlc>' => ['dollar', 'cent', 'money', 'USD', 'GBP', 'pound', 'pence', 'euro', 'yen', '\$', '¢', '€', '£', '¥'],
		'<offsite>' => ['offsite', 'Facebook', 'flightrising', 'dragcave', 'dragoncave', 'FB', 'FR', 'FV', 'furvilla', 'DC'],
	];
	private $context_blocks = [
		'Offsite Sales' => ['<sales>', '<site content>', '<offsite>'],
		'RLC Sales' => ['<sales>', '<site content>', '<rlc>'],
		'Offsite Trades' => ['<trades>', '<site content>', '<offsite>'],
		'RLC Trades' => ['<trades>', '<site content>', '<rlc>'],
	];



	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		// Letter Subs
		foreach($this->letter_subs as $letter => $sub) {
			$sub_list = json_encode($sub);
			$regex = Filter::regexifySubs($sub);
			LetterSubs::create(['letter' => $letter, 'subs' => $sub_list, 'regex' => $regex]);
		}


		// Blacklist
		$endings = '';
		foreach($this->blacklist as $type => $words) {
			$subbed = Filter::regexifyWords($words);
			if($type == 'ending') $endings = implode('|', $subbed);
			foreach($words as $key => $word) {
				Blacklist::create(['word' => $word, 'filter_type' => $type, 'subbed' => $subbed[$key]]);
			}
		}
		// Whitelist
        foreach($this->whitelist as $word) {
            Whitelist::create(['word' => $word]);
        }


		// Contexts
		foreach($this->word_context as $context => $words) {
			$word_list = json_encode($words);
			$subbed = '/(?:'.implode('|', Filter::regexifyWords($words)).')(?:'.$endings.')*/i';

			Context::create(['context' => $context, 'words' => $word_list, 'subbed' => $subbed]);
		}
		// Context Blocks
		foreach($this->context_blocks as $nickname => $contexts) {
			$context_list = json_encode($contexts);
			ContextBlock::create(['nickname' => $nickname, 'contexts' => $context_list]);
		}
	}
}
