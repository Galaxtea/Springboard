<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Middleware\Authenticate as MainAuth;
use Illuminate\Auth\AuthenticationException;

class Authenticate extends MainAuth
{
	protected function unauthenticated($request, array $guards) {
		// Except is specifically for the Fortify auth routes.
		// Otherwise use ->withoutMiddleware(['auth']) on the route/group definition in routes/
		$except = ['password.*', 'login', 'login.*', 'register', 'register.*', 'password.*', 'two-factor.*'];
		$is_excepted = false;
		foreach($except as $name) {
			if($request->routeIs($name)) {
				$is_excepted = true;
				break;
			}
		}


		if(!$is_excepted) {
			throw new AuthenticationException(
				'unauthenticated.',
				$guards,
				$this->redirectTo($request)
			);
		}

		return $request;
	}
}
