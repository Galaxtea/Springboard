<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

use App\Models\User\User;
use App\Services\User\UserService;

use App\Http\Controllers\Controller;
class UserController extends Controller
{
	public function getProfile($user_id) {
		$user = User::find($user_id);

		if(!$user || ($user->settings->private_profile && !parent::$is_auth)) return abort(404, "That user doesn't appear to exist.");
		return view('user.profile', ['profile' => User::find($user_id)]);
	}

	public function getSettings() {
		return view('user.settings');
	}

	public function updateSettings(Request $request, UserService $service) {
		// $service->
	}





	public function blockList() {
		if(!parent::$is_auth) return abort(404, 'You need to be logged in to do that.');

		return view('user.block_list', ['blocks' => (parent::$user->blockList())->with('isBlocking')->paginate()]);
	}

	public function blockUser(Request $request, UserService $service, $user_id) {
		if(!parent::$is_auth) return abort(404, 'You need to be logged in to do that.');

		$blocker = parent::$user;
		if($user_id != $blocker->id) {
			$service->blockUser($blocker, $user_id, $request['self_note']);
		}

		return back();
	}
	public function unblockUser(UserService $service, $user_id) {
		if(!parent::$is_auth) return abort(404, 'You need to be logged in to do that.');

		$blocker = parent::$user;
		if($blocker->findBlock($user_id)) {
			$service->unblockUser($blocker, $user_id);
		}

		return back();
	}




	
	public function friendList() {
		if(!parent::$is_auth) return abort(404, 'You need to be logged in to do that.');

		return view('user.friend_list', ['friends' => parent::$user->friendedUsers()->paginate(), 'pending' => (parent::$user->pendingFriends())->with('isRequesting')->get()]);
	}

	public function friendUser(UserService $service, $user_id) {
		if(!parent::$is_auth) return abort(404, 'You need to be logged in to do that.');

		$friender = parent::$user;
		if($user_id != $friender->id && !$friender->isBlocked($user_id)) {
			$service->friendUser($friender, $user_id);
		}

		return back();
	}
	public function unfriendUser(UserService $service, $user_id) {
		if(!parent::$is_auth) return abort(404, 'You need to be logged in to do that.');

		$friender = parent::$user;
		$service->unfriendUser($friender, $user_id);

		return back();
	}
}
