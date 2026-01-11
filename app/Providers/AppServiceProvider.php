<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

use App\Helpers\HtmlExtended;
use Spatie\Html\Html;
use Carbon\Carbon;
use Config;

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
    }
}
