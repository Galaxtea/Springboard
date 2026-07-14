<?php

namespace App\Http\Controllers\User;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;

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
		if($request['rules']) {
			// Bot trap - this checkbox should NOT be checked if it's a normal user.
		} elseif($exists = User::where('email', $request['email'])->first()) {
			// The email is already in use, so we quietly fake the reg request, but send an alert to the used email.
			$exists->notify(new ExistingEmail());
		} elseif($user = (new RegService)->register($request->validated())) {
			// We registered successfully.
			event(new Registered($user));
		} else {
			return back()->withInput();
		}
		return to_route('login')->with(['status' => __("auth.verify")]);
	}


	public function getForgotPassword() {
		return view('auth.forgot-password');
	}
	public function postForgotPassword(Request $request) {
		$request->validate(['email' => 'required|email']);
		Password::sendResetLink($request->only('email'));

		// To prevent email enumeration, we always give the same response message.
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
}
