<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use App\Http\Controllers\Controller;
class EmailVerifyController extends Controller
{
	public function __construct() {
		$this->verified = auth()->user()->is_verified;
		if($this->verified) $this->redirect = to_route('home')->with(['status' => __("auth.verified")]);
	}


	public function getNeedEmailVerify() {
		if($this->verified) return $this->redirect;
		return view('auth.verify-email');
	}
	public function postSendVerifyEmail(Request $request) {
		if($this->verified) return $this->redirect;
		auth()->user()->sendEmailVerificationNotification();
		return back()->with(['status' => __("auth.verify")]);
	}
	public function getVerifyingEmail(EmailVerificationRequest $request) {
		if($this->verified) return $this->redirect;
		$request->fulfill();
		return to_route('home')->with(['status' => __("auth.verified")]);
	}
}
