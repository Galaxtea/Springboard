<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

use App\Models\User\User;
use App\Models\Admin\IPHistory;
use App\Services\Admin\UserService;

use App\Http\Controllers\AdminController;
class UserController extends AdminController
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(UserService $service) {
		parent::__construct();
		$this->service = $service;
	}





	public function getUserList() {
		return view('admin.user.list', ['players' => $this->service->getAll()]);
	}
	public function getUser($id) {
		return view('admin.user.user', ['player' => User::with('settings')->find($id), 'recent_ip' => IPHistory::where('user_id', $id)->orderBy('updated_at', 'DESC')->first()]);
	}

	public function getUserIPs($id) {
		$history = IPHistory::where('user_id', $id)->orderBy('updated_at', 'DESC')->paginate();
		return view('admin.user.ips', ['player' => User::find($id), 'ips' => $history]);
	}
	public function getIPUsers($ip) {
		$uses = IPHistory::where('ip_address', '=', $ip)->orderBy('updated_at', 'DESC')->paginate();
		return view('admin.ips', ['uses' => $uses]);
	}
}
