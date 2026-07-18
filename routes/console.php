<?php

use Illuminate\Support\Facades\Schedule;


// Removes expired password reset tokens from the database
Schedule::command('auth:clear-resets')->everyFifteenMinutes();


// Allows ips to be logged for the new day
Schedule::call(function() {
	cache()->tags(['ip_history'])->flush();
})->daily()->timezone(config('site_settings.site_time'))->evenInMaintenanceMode();