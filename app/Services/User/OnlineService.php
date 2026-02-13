<?php

namespace App\Services\User;

use Exception;
use App\Services\Service;

use Carbon\Carbon;
use App\Models\User\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;

class OnlineService extends Service
{
	public static function list($with_invis = false, $page) {
		return Cache::tags(['online', "invis:{$with_invis}"])->flexible("page_{$page}", [300, 900], function () use ($with_invis) {
			$query = self::findOnline($with_invis);

			return $query->orderBy('active_at', 'DESC')->paginate();
		});
	}

	public static function count() {
		return Cache::tags(['online'])->flexible('count', [30, 300], function () {
			return self::findOnline(true)->count();
		});
	}

	private static function findOnline($with_invis = false) {
		$query = User::select('username', 'id', 'active_at')->where('active_at', '>=', Carbon::now()->subMinutes(15));
		if(!$with_invis) $query->whereHas('settings', function (Builder $query) {
			$query->where('display_active', '1');
		});
		return $query;
	}
}