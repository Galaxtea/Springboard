<?php

namespace App\Helpers;

use Auth;

abstract class AuthShare {

	public function __construct() {
		self::getAuth();
	}



	/*
	 | Share user auth
	 |----------------------------*/
		protected static $is_auth;
		protected static $user;

		private static function getAuth(): void {
			if(!isset(self::$is_auth)) {
				self::$is_auth = Auth::check();
				self::$user = Auth::user();
			}
			return;
		}
}