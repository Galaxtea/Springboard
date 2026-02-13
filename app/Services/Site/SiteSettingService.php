<?php

namespace App\Services\Site;

use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\Cache;

use App\Models\Site\Setting;

class SiteSettingService extends Service
{
	public static function get(string $key) {
		return Cache::tags(['settings'])->rememberForever("{$key}", function () use ($key) {
			return Setting::where('ref_key', $key)->first();
		});
	}

	public function set(array $data) {
		\DB::beginTransaction();

		try {
			if(!$setting = $this->get($data['ref_key'])) throw new Exception('There is no setting called '.$data['ref_key'].'.');

			$setting->update(['value' => $data['value'], 'text' => $data['text']]);

			Cache::tags(['settings'])->forever("{$data['ref_key']}", $setting);

			return $this->commitReturn($setting);
		} catch (Exception $e) {
			$this->errors->merge($e->toArray());
		}
		return $this->rollbackReturn();
	}
}