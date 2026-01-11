<?php

namespace Database\Seeders;

use App\Models\User\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Actions\Fortify\CreateNewUser;

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
		$newUser = new CreateNewUser;
		foreach ($this->users as $user_data) {
			$user = $newUser->create([
				'username' => $user_data[0],
				'email' => $user_data[0] . '@springboard.nonexistentwebsite',
				'password' => strtolower($user_data[0]).'potatoes',
				'password_confirmation' => strtolower($user_data[0]).'potatoes',
				'birthday' => '2000-05-05',
				'tos' => true,
				'privacy' => true
			]);
			$user->update(['rank_id' => $user_data[1]]);
		}
	}
}
