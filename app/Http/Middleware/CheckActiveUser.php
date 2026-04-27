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
		if($user = auth()->user()) $user->touchActive($request->ip());
		view()->share('user', $user);


		// Encoding then decoding quickly converts the multidimensional array into an object
		$currencies = json_decode(json_encode(config('site_settings.currencies')));
		view()->share('currencies', $currencies);


		return $next($request);
	}
}
