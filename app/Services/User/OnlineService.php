<?php

namespace App\Services\User;

use Exception;
use App\Services\Service;

use Carbon\Carbon;
use App\Models\User\User;
use Illuminate\Support\Facades\Cache;

class OnlineService extends Service
{
	public static function list($with_invis = false) {
		$query = User::join('user_settings', 'users.id', '=', 'user_settings.user_id')->select('username', 'users.id', 'active_at', 'user_settings.display_active')->where('active_at', '>=', Carbon::now()->subMinutes(15));

		if(!$with_invis) $query->where('user_settings.display_active', '1');
		return $query->orderBy('active_at', 'DESC');
	}

	public static function count() {
		return Cache::flexible('online_count', [30, 300], function () {
			return self::list(true)->count();
		});
	}
}