<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;



use Config;
use Carbon\Carbon;
use App\Models\User\User;
use Illuminate\Support\Facades\Cache;



use App\Services\Site\SiteService;



class CheckActiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $is_auth = Auth::check();
        $user = $is_auth ? Auth::user() : null;
        if($user) $user->touchActive();


        view()->share('is_auth', $is_auth);
        view()->share('user', $user);

        view()->share('time', Carbon::siteClock());
        view()->share('online_count', SiteService::countOnline());







        $fresh = 1 * 60;
        $stale = 5 * 60;


        $currencies = (object) [
                'primary_name' => Config::get('site_settings.pri_curr'),
                'secondary_name' => Config::get('site_settings.sec_curr'),
                'primary_abbr' => Config::get('site_settings.pri_abbr'),
                'secondary_abbr' => Config::get('site_settings.sec_abbr'),
            ];


        view()->share('currencies', $currencies);



        return $next($request);
    }
}
