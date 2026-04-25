<?php

namespace App\Services\User;

use DB;
use Config;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

use App\Models\User\User;
use App\Models\Site\RegCode;
use App\Models\Token\EmailVerifyToken as EmailToken;

use App\Services\Service;
class UserRegistrationService extends Service
{
	public function register($data) {
		if($user = $this->commitUser($data)) { // A user account has successfully been added, so proceed
			event(new Registered($user));

			if(isset($data['reg_code'])) $this->useRegCode($data['reg_code'], $user->id);
			if($data['referrer'] != null) $this->creditReferrer($data['referrer'], $user);

			return $user;
		}
		return false;
	}


	private function commitUser($data) {
		DB::beginTransaction();

		try {
			// Add in extra data
				$data['pri_curr'] = Config::get('site_settings.pri_start');
				$data['sec_curr'] = Config::get('site_settings.sec_start');
				$data['password'] = Hash::make($data['password']);
				$data['was_referred'] = $data['referrer'] ? 1 : 0;


			// Create all of the relevant User DB rows
				$user = User::create($data);

				$user->settings()->create($data);

				$user->stats()->create([]);
				$user->profile()->create([]);

			DB::commit();
			return $user;
		} catch (Exception $e) {
			DB::rollback();
			$this->errors->merge(['Unable to register' => $e->getMessage()]);
		}
		return false;
	}



	private function useRegCode($code, $user_id): void {
		$invite = RegCode::where('token', $code)->first();
		$invite->update([
			'is_used' => 1,
			'user_id' => $user_id
		]);
	}

	private function creditReferrer($referrer, $user): void {
		$referrer = User::select('username', 'id')->where('username', $referrer)->first();

		DB::beginTransaction();
		try {
			$referrer->stats()->increment('referrals');
			$user->referrer()->create(['referred_by' => $referrer->id]);

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			$this->errors->merge(['Unable to credit referrer' => $e->getMessage()]);
		}
	}
}