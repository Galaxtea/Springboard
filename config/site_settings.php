<?php


return [

	/*
	|--------------------------------------------------------------------------
	| Timezone
	|--------------------------------------------------------------------------
	|
	| The site's backend uses UTC to avoid issues with DST,
	| but you can use this setting to adjust the frontend clock.
	| You can enter a timezone (eg 'America/New_York') or a number (eg -5)
	|
	*/

	'site_time' => 5,
	'time_format' => 'jS F, Y \a\t g:ia',



	/*
	|--------------------------------------------------------------------------
	| Currencies
	|--------------------------------------------------------------------------
	|
	| These settings will determine what your currency names
	| are displayed as across the site. If you don't want to use
	| abbreviations, just make them match the full name.
	|
	*/

	'currencies' => [
		'primary' => [
			'name' => 'Coins',
			'shorthand' => 'C',
			'start_amount' => 7500,
		],
		'secondary' => [
			'name' => 'Gems',
			'shorthand' => 'G',
			'start_amount' => 10,
		],
	],



	/*
	|--------------------------------------------------------------------------
	| Age Restriction
	|--------------------------------------------------------------------------
	|
	| The required age of users (in years) to be able to
	| register an account on the site. There are legal
	| minimums depending on where your users are from,
	| so bear those in mind.
	|
	*/

	'age_required' => 16,



	/*
	|--------------------------------------------------------------------------
	| Trusted Hosts
	|--------------------------------------------------------------------------
	|
	| This helps secure the site against cross-site requests.
	| Each trusted host should be entered as a regex string.
	| Subdomains for the listed hosts are automatically trusted.
	| If you don't want this, un-comment the subdomains part of
	| the trustHosts function in the bootstrap/app.php file.
	|
	*/

	'trusted_hosts' => [
		'^lanterns\.click$',
	],

];