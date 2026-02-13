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

// Come back to these...
// use Limit;
// use RateLimiter;
// use Request;

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


		// Access Gates
			Gate::define('can-panel', function (User $user) {
				return $user->perms('can_panel') ? Response::allow() : Response::denyAsNotFound();
			});
	}
}
