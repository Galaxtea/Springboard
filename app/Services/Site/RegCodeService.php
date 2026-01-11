<?php

namespace App\Services\Site;

use Str;
use Exception;
use App\Services\Service;

use App\Models\Site\RegCode;

class RegCodeService extends Service
{
	public function get(string $token) {
		if($invite = RegCode::where('token', $token)->first()) return $invite;
		return null;
	}

	public function getList($used = "unused") {
		$codes = RegCode::select('*');
		if($used == "unused") return $codes->where('is_used', 0)->paginate();
		else return $codes->where('is_used', 1)->paginate();
	}

	public function massCreate($count, $user_id) {
		\DB::beginTransaction();

		try {
			$codes = [];
			if($count < 1) throw new Exception("You cannot generate less than one code at a time.", 1);
			if($count > 20) throw new Exception("The maximum number of codes to generate at once is 20.", 1);

			for($i=0; $i < $count; $i++) { 
				$codes[] = $this->generate($user_id)->token;
			}
			
			return $this->commitReturn($codes);
		} catch (Exception $e) {
			if($e->getMessage()) $this->setError('reg_code', $e->getMessage());
			else $this->setError('reg_code', 'Unable to create the registration codes, please try again.');
		}
		return $this->rollbackReturn();
	}

	public function generate($user_id) {
		\DB::beginTransaction();

		try {
			$code = RegCode::create(['token' => Str::random(14), 'maker_id' => $user_id]);
			
			return $this->commitReturn($code);
		} catch (Exception $e) {}
		return $this->rollbackReturn();
	}

	public function quickUse($reg_code) {
		\DB::beginTransaction();

		try {
			if(!$invite = $this->get($reg_code)) throw new Exception('The registration code is invalid.');
			if($invite->is_used) throw new Exception('That registration code has already been used.');
			
			$invite->update(['is_used' => 1]);

			return $this->commitReturn();
		} catch (Exception $e) {
			if($e->getMessage()) $this->setError('reg_code', $e->getMessage());
			else $this->setError('reg_code', 'Unable to use registration code, please try again.');
		}
		return $this->rollbackReturn();
	}

	public function updateUse($user, $reg_code) {
		\DB::beginTransaction();

		try {
			if(!$invite = $this->get($reg_code)) throw new Exception('The registration code is invalid.');
			if(!$invite->is_used) throw new Exception('That registration code has not been used yet.');
			
			$invite->update(['user_id' => $user->id]);

			return $this->commitReturn();
		} catch (Exception $e) {
			if($e->getMessage()) $this->setError('reg_code', $e->getMessage());
			else $this->setError('reg_code', 'Unable to use registration code, please try again.');
		}
		return $this->rollbackReturn();
	}

	public function delete($code) {
		try {
			if(!$code) return true;
			if(!$code->delete()) throw new Exception("Error Processing Request", 1);

			return true;
		} catch (Exception $e) {}
		return false;
	}
}