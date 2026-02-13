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
	public function index(Request $request, SiteNewsService $service) {
		return view('index', ['news_posts' => $service->getLatest($request->query('page', '1'))]);
	}

	public function online(Request $request) {
		// Check if the viewer is a staff with perms to see "invisible" users
		return view('online', ['online_list' => OnlineService::list(auth()->user()?->perms('can_reports'), $request->query('page', '1'))]);
	}
}
