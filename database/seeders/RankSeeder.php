<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use DB;

class RankSeeder extends Seeder
{
	private $permissions = [
		['Can Ban', 'Allows the user to manage site and forum bans.'],
		['Can Promote', 'Allows the user to promote and demote other users. Owners can only be demoted by other Owners.'],
		['Can Marking', 'Allows the user to manage available markings on the site.'],
		['Can Maint', 'Allows the user to login and use the site during maintenance.'],
		['Can Panel', 'Gives the user access to the admin panel.'],
		['No Ads', 'Removes Ads for the user.'],
		['Can Tag', 'Allows the user to mark content with important tags.'],
		['Forum Boost', 'Allows the user to see, post, or start threads in forum boards requiring this perm.'],
		['Can Reports', 'Allows the user to manage and handle reports.'],
		['Can Msg Mod', 'Allows the user to manage all messages and forum posts such as editing and deleting, regardless of post ownership.'],
		['Block Bypass', 'Allows the user to bypass blocks to view a user\'s profile and posts.'],
	];

	private $ranks = [
		['User', '000000', 'A player of the site.', 0, 0, '0'],
		['Premium User', '000000', 'A player of the site that has upgraded for premium.', 0, 0, '0'],
		['Owner', '000000', 'The owner of the site.', 1, 1, '100'],
		['Admin', '000000', 'An administrator to do general high-level tasks.', 1, 1, '95'],
		['Moderator', '000000', 'A moderator to do basic moderation tasks and handle reports.', 1, 0, '90'],
		['Coder', '000000', 'A coder or programmer.', 1, 1, '85'],
		['Artist', '000000', 'An official site artist.', 1, 0, '85'],
	];

	private $rank_perms = [ // This uses the DB id, not the array index
		[2, 6],
		[3, 1], [3, 2], [3, 3], [3, 4], [3, 5], [3, 6], [3, 7], [3, 8], [3, 9], [3, 10], [3, 11],
		[4, 1], [4, 2], [4, 3], [4, 4], [4, 5], [4, 6], [4, 7], [4, 8], [4, 9], [4, 10], [4, 11],
		[5, 1], [5, 4], [5, 5], [5, 6], [5, 8], [5, 9], [5, 10], [5, 11],
		[6, 1], [6, 2], [6, 3], [6, 4], [6, 5], [6, 6], [6, 11],
		[7, 6],
	];


	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$perms = DB::table('permissions');
			foreach ($this->permissions as $key => $permission) {
				$permission = [
					'name' => $permission[0],
					'slug' => implode('_', explode(' ', strtolower($permission[0]))),
					'description' => $permission[1],
				];
				$this->permissions[$key] = $permission;
			}
			$perms->insert($this->permissions);

		$roles = DB::table('ranks');
			foreach ($this->ranks as $key => $rank) {
				$rank = [
					'name' => $rank[0],
					'slug' => implode('_', explode(' ', strtolower($rank[0]))),
					'color' => $rank[1],
					'description' => $rank[2],
					'is_staff' => $rank[3],
					'is_admin' => $rank[4],
					'power' => $rank[5],
				];
				$this->ranks[$key] = $rank;
			}
			$roles->insert($this->ranks);

		$role_perms = DB::table('rank_permissions');
			foreach ($this->rank_perms as $key => $pair) {
				$pair = [
					'rank_id' => $pair[0],
					'permission_id' => $pair[1],
				];
				$this->rank_perms[$key] = $pair;
			}
			$role_perms->insert($this->rank_perms);
	}
}
