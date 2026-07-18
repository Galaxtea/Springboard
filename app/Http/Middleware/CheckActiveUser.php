<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveUser
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
	 */
	public function handle(Request $request, Closure $next): Response
	{
		if($user = auth()->user()) {
			$user->touchActive();

			$ip = $request->ip();
			cache()->tags(['ip_history', 'user:'.$user->id])->remember('ip:'.$ip, now()->plus(hours: 24), function() use ($user, $ip) {
				return \App\Models\Admin\IPHistory::create(['user_id' => $user->id, 'ip_address' => $ip]);
			});
		}
		view()->share('user', $user);








		// Encoding then decoding quickly converts the multidimensional array into an object
		$currencies = cache()->rememberForever('currencies', function() {return json_decode(json_encode(config('site_settings.currencies')));});
		view()->share('currencies', $currencies);


		return $next($request);
	}
}
