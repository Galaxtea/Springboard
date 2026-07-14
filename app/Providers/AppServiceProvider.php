<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Validation\Rules\Password;

use Carbon\Carbon;
use Config;

use Route;

use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use App\Models\User\User;


use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

use Spatie\Html\Html;
use App\Helpers\HtmlExtended;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 */
	public function register(): void
	{
		$this->app->singleton(Html::class, HtmlExtended::class);
	}

	/**
	 * Bootstrap any application services.
	 */
	public function boot(): void
	{
		Password::defaults(function () {
			$rule = Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised();

			// If we're working on the site locally, we don't need to worry about passwords.
			return $this->app->isProduction() ? $rule : Password::min(1);
		});

		Carbon::macro('siteClock', static function() {
			$date = self::this()->tz(Config::get('site_settings.site_time'));
			return $date->format('M').' <i class="cal">'.$date->format('j').'</i>'.$date->format('g:ia');
		});

		Route::pattern('id', '[0-9]+');


		// Rate Limiting
			RateLimiter::for('login', function (Request $request) {
				return [
					Limit::perMinute(500),
					Limit::perMinute(3)->by($request->ip()),
					Limit::perMinute(3)->by(strtolower($request->input('email')))->response(function () {
						return back()->withInput()->with(['status' => __('ratelimit.default')]);
					}),
				];
			});

			RateLimiter::for('register', function (Request $request) {
				return Limit::perMinute(5)->by($request->ip())->response(function () {
					return back()->withInput()->with(['status' => __('ratelimit.default')]);
				});
			});

			RateLimiter::for('forgot-password', function (Request $request) {
				return [
					Limit::perMinute(500),
					Limit::perMinute(3)->by($request->ip()),
				];
			});



		// Access Gates
			Gate::define('can_panel', function (User $user) {
				return $user->perms('can_panel') ? Response::allow() : Response::denyAsNotFound();
			});
	}
}
