<?php

namespace App\Http\Controllers\Forum;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

use App\Services\Forum\ForumService;

use App\Http\Controllers\Controller;
class ForumController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(ForumService $service) {
		// $this->middleware('auth')->except('index');
		$this->service = $service;
	}

	public function index() {
		$data = ['parent_board' => null];
		if(parent::$is_auth) $data['user'] = parent::$user;
		$forums = $this->service->getForums($data)->groupBy('cat_name');

		return view('forums.index', ['forums' => $forums]);
	}

	public function getBoard($slug) {
		$board = $this->service->getBoard($slug);
		$threads = $board->threads()->paginate();

		if(!$board || !$board->checkPerms()) abort(404);

		return view('forums.board', ['board' => $board, 'threads' => $threads]);
	}
}
