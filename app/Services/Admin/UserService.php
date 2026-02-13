<?php

namespace App\Services\Admin;

use Exception;
use App\Services\Service;

use App\Models\User\User;

class UserService extends Service
{
	public function getAll() {
		return User::paginate();
	}
}