<?php

namespace Database\Seeders;

use App\Models\Site\Setting;
use App\Models\Admin\ReportCategories;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
	use WithoutModelEvents;

	private $settings = [
		['maintenance', 0, 'The site is currently under maintenance. Sorry.'],
		['open_reg', 1, 'Registration is currently closed.'],
		['invite_gen', 0, 'Enable to allow users to generate invite codes.'],
	];

	private $report_cats = [
		'Another User' => [
			['Underage', ''],
			['Multi-Accounting', ''],
			['Inappropriate Name', ''],
			['Block Evasion', ''],
			['Scammed Me', ''],
			['Stalked or Harassed Me', ''],
			['Impersonating Users or Staff', ''],
			['Inappropriate Staff Conduct', ''],
			['Broke Site Rules', ''],
		],
		'My Account' => [
			['Unable to Verify', ''],
			['Unable to Reset Password', ''],
			['Appeal a Ban', ''],
			['Appeal a Warning', ''],
			['Request an IP Exception', 'admin_only'],
		],
		'User-Written Content' => [
			['Inappropriate Conduct', ''],
			['Offering Real-Life-Currency Sales', ''],
			['Offering Banned Cross-Site Trades', ''],
			['Evading Profanity Filter', ''],
		],
		'Adoptables or Pets' => [
			['Inappropriate Name', ''],
			['Image Issues', ''],
			['Missing Adopts', ''],
		],
		'Inventory or Items' => [
			['Image Issues', ''],
			['Missing Items', ''],
		],
		'Website Bug or Exploit' => [
			['Discuss Potential Bug', ''],
			['Report an Exploit', 'file_access'],
		],
		'Premium Currency' => [
			['Haven\'t Received Purchased Currency', 'admin_only'],
		],
		'Other' => [
			['Typo in Site Content', 'file_access'],
			['Unsure or Doesn\'t Fit Listed Options', ''],
		],
	];

	/**
	 * Seed the application's database.
	 */
	public function run(): void
	{
		foreach ($this->settings as $setting) {
			Setting::create([
				'ref_key' => $setting[0],
				'value' => $setting[1],
				'text' => $setting[2]
			]);
		}

		foreach($this->report_cats as $key => $cats) {
			foreach($cats as $cat) {
				ReportCategories::create([
					'main_cat' => $key,
					'sub_cat' => $cat[0],
					'perm_req' => $cat[1],
				]);
			}
		}
	}
}
