<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

use App\Models\User\User;
use App\Models\User\UserSettings;

use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\User\UserRegistrationService as RegService;
use App\Services\AuthService;

use App\Notifications\ExistingEmail;

use App\Http\Controllers\Controller;
class AuthController extends Controller
{
	// Guest-specific
		public function getLogin() {
			return view('auth.login');
		}
		public function postLogin(LoginUserRequest $request) {
			if(Auth::attempt($request->safe()->only(['email', 'password']), $request['remember'])) {
				$request->session()->regenerate();
				return redirect()->intended('/');
			}
			return back()->withInput()->withErrors(['login' => __("auth.failed")]);
		}


		public function postLogout(Request $request) {
			Auth::logout();
			$request->session()->invalidate();
			$request->session()->regenerateToken();
			return to_route('home');
		}


		public function getRegister() {
			// Check the site settings here instead of hard-coded :)
			return view('auth.register', ['open_reg' => false]);
		}
		public function postRegister(RegisterUserRequest $request) {
			// We've validated the registration info already, but we need to quietly handle
			// conflicting emails to prevent email enumeration through the registration form
			if($exists = User::where('email', $request['email'])->first()) {
				// The email is already in use, so we quietly fake the reg request, but send an alert to the used email
				$exists->notify(new ExistingEmail());
				return to_route('login')->with(['status' => __("auth.verify")]);
			}

			$reg_service = new RegService;
			if($user = $reg_service->register($request->validated())) {
				return to_route('login')->with(['status' => __("auth.verify")]);
			}
			return back()->withInput();
		}


		public function getForgotPassword() {
			return view('auth.forgot-password');
		}
		public function postForgotPassword(Request $request) {
			$request->validate(['email' => 'required|email']);

			$status = Password::sendResetLink($request->only('email'));

			return back()->withInput()->with(['status' => __("passwords.sent")]);
		}
		public function getResetPassword($token) {
			return view('auth.reset-password', ['token' => $token]);
		}
		public function postResetPassword(ResetPasswordRequest $request) {
			$status = Password::reset(
				$request->only('email', 'password', 'password_confirmation', 'token'),
				function (User $user, string $password) {
					$user->forceFill(['password' => Hash::make($password)])->setRememberToken(Str::random(60));
					$user->save();
					event(new PasswordReset($user));
				}
			);

			// The passwords.user status message from Laravel can be used to expose user emails,
			// which we don't want, so we make it mimic an invalid token status instead.
			if($status == "passwords.user") $status = "passwords.token";

			return $status === Password::PasswordReset
					? redirect()->route('login')->with(['status' => __("passwords.reset")])
					: back()->withErrors(['email' => [__($status)]]);
		}




	// User-specific
		public function getNeedEmailVerify() {
			if(auth()->user()->is_verified) return to_route('home')->with(['status' => __("auth.verified")]);

			return view('auth.verify-email');
		}
		public function postSendVerifyEmail(Request $request) {
			if(auth()->user()->is_verified) return to_route('home')->with(['status' => __("auth.verified")]);

			auth()->user()->sendEmailVerificationNotification();
			return back()->with(['status' => __("auth.verify")]);
		}
		public function getVerifyingEmail(EmailVerificationRequest $request) {
			if(auth()->user()->is_verified) return to_route('home')->with(['status' => __("auth.verified")]);

			$request->fulfill();
			return to_route('home')->with(['status' => __("auth.verified")]);
		}
}
