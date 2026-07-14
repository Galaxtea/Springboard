<?php

namespace App\Services\User;

use DB;
use Exception;
use Illuminate\Support\Facades\Hash;

use App\Models\User\User;
use App\Models\Site\RegCode;
use App\Models\Token\EmailVerifyToken as EmailToken;

use App\Services\Service;
class UserRegistrationService extends Service
{
	public function register($data) {
		if($user = $this->commitUser($data)) { // A user account has successfully been added, so proceed
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
				$data['pri_curr'] = config('site_settings.currencies.primary.start_amount');
				$data['sec_curr'] = config('site_settings.currencies.secondary.start_amount');
				$data['password'] = Hash::make($data['password']);
				$data['was_referred'] = $data['referrer'] ? 1 : 0;
				$data['active_at'] = \Carbon\Carbon::now();


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