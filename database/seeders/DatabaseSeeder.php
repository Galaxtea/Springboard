<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use DB;

class DatabaseSeeder extends Seeder
{
	use WithoutModelEvents;

	/**
	 * Seed the application's database.
	 */
	public function run(): void
	{
		$this->call([
			SettingsSeeder::class,
			RankSeeder::class,
			UserSeeder::class,
			ContentSeeder::class,
			TagSeeder::class,
			ProfanitySeeder::class,
		]);

		DB::table('site_settings')->where('ref_key', 'open_reg')->update(['value' => '0']);
	}
}
