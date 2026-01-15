<?php

namespace App\Http\Controllers;

use Config;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

use App\Services\User\OnlineService;
use App\Services\Site\SiteNewsService;

use App\Http\Controllers\Controller;
class HomeController extends Controller
{
	public function index(SiteNewsService $service) {
		return view('front-page', ['news_posts' => $service->getLatest()]);
	}

	public function online() {
		// Check if the viewer is a staff with perms to see "invisible" users
		return view('online', ['online_list' => OnlineService::list((parent::$is_auth ? parent::$user->perms('can_reports') : false))->paginate()]);
	}
}
