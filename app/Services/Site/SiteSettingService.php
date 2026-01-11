<?php

namespace App\Services\Site;

use Exception;
use App\Services\Service;

use App\Models\Site\SiteSetting;

class SiteSettingService extends Service
{
	public function get(string $key) {
		if($setting = SiteSetting::where('ref_key', $key)->first()) return $setting;
		return null;
	}

	public function set(array $data) {
		\DB::beginTransaction();

		try {
			if(!$setting = $this->get($data['ref_key'])) throw new Exception('There is no setting called '.$data['ref_key'].'.');

			$setting->update(['value' => $data['value'], 'text' => $data['text']]);

			return $this->commitReturn($setting);
		} catch (Exception $e) {
			$this->errors->merge($e->toArray());
		}
		return $this->rollbackReturn();
	}
}