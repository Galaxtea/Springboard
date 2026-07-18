<?php

namespace Database\Seeders;

use App\Models\User\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
	use WithoutModelEvents;

	private $users = [
		['Galaxtea', '3'],
		['Retro_Dino', '5'],
		['Ochre', '1'],
		['Campfire', '2'],
		['Dalmatian', '4'],
		['Dingo', '3'],
		['Wooper', '6'],
		['Sashimi', '7'],
	];

	/**
	 * Seed the application's database.
	 */
	public function run(): void
	{
		$now = \Carbon\Carbon::now();
		foreach ($this->users as $user_data) {
			$user = User::create([
				'username' => $user_data[0],
				'email' => strtolower($user_data[0]).'@springboard.nonexistentwebsite',
				'password' => Hash::make(strtolower($user_data[0]).'potatoes'),
				'active_at' => $now,
				'email_verified_at' => $now
			]);

			$user->settings()->create(['birthday' => '2000-05-05']);

			$user->stats()->create([]);
			$user->profile()->create([]);

			$user->update(['rank_id' => $user_data[1]]);
		}
	}
}
