<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use DB;

class TagSeeder extends Seeder
{
	private $tags = [
		// ['name',		'dis',	'type',					'req_perm',		'rank',	'CSS style id',	'description'],
		['Tracking',    'T',    'forums, staff',        'can_msg_mod',	null,	'important',	'This thread is being tracked by a staff member.'],
		['Important',   'I',    'forums, staff',        'can_msg_mod',	null,	'important',	'This thread is important, please make sure to read it!'],
		['Buying',      'B',    'forums, forum-sale',   null,			null,	'sales',		'description'],
		['Selling',     'S',    'forums, forum-sale',   null,			null,	'sales',		'description'],
		['Auction',     'A',    'forums, forum-sale',   null,			null,	'sales',		'description'],
		['Trading',     'T',    'forums, forum-sale',   null,			null,	null,			'description'],
		['Raffle',      'R',    'forums, forum-sale',   null,			null,	null,			'description'],
		['Giveaway',    'G',    'forums, forum-sale',   null,			null,	null,			'description'],
	];

	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$table = DB::table('content_tags');
		foreach($this->tags as $data) {
			$tag = [
				'name' => $data[0],
				'display_name' => $data[1],
				'type' => $data[2],
				'req_perm' => $data[3],
				'req_rank' => $data[4],
				'color' => $data[5],
				'description' => $data[6],
			];

			$table->insert($tag);
		}
	}
}
