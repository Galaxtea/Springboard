<?php

namespace App\Services\Admin;

use Exception;
use App\Services\Service;

use Cache;
use App\Models\Admin\MainReport;

class ReportService extends Service
{
	public static function count() {
		return Cache::tags(['reports'])->flexible('open_count', [30, 300], function () {
			return MainReport::select('status')->whereNot('status', 'closed')->count();
		});
	}
}