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

    'pri_curr' => 'Coins',
    'pri_abbr' => 'C',
    'pri_start' => 5000,

    'sec_curr' => 'Gems',
    'sec_abbr' => 'G',
    'sec_start' => 0,



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

];