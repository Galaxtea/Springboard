<?php

namespace App\Services\User;

use Exception;
use App\Services\Service;

use App\Models\User\User;
use App\Models\User\FriendedUsers as Friends;
use App\Helpers\BBCodeParser as BBCode;

class UserService extends Service
{
	public function get($id) {
		return User::find($id);
	}

	public function update() {}




	public function blockUser($blocker, $user_id, $self_note = null) {
		\DB::beginTransaction();

		try {
			$blocker->blockList()->upsert([
				'blocker_id' => $blocker->id,
				'blocked_id' => $user_id,
				'self_note' => $self_note
			],
			['blocker_id', 'blocked_id'],
			['self_note']);

			// Remove any existing friends
			if($friend = $blocker->friendList()->where('friended_id', $user_id)) $friend->delete();
			if($friended = Friends::where('friend_id', $user_id)->where('friended_id', $blocker->id)) $friended->delete();

			$this->commitReturn();
		} catch (Exception $e) {
			$this->rollbackReturn();
		}
		return;
	}
	public function unblockUser($blocker, $user_id) {
		\DB::beginTransaction();

		try {
			$blocker->blockList()->where('blocked_id', '=', $user_id)->delete();
			$this->commitReturn();
		} catch (Exception $e) {
			$this->rollbackReturn();
		}

		return;
	}




	public function friendUser($friender, $user_id) {
		// Are we sending a new request, or accepting an inbound request?
		$inRequest = Friends::where('friend_id', '=', $user_id)->where('friended_id', '=', $friender->id)->first();

		\DB::beginTransaction();
		try {
			if($inRequest && $inRequest->status === 'Pending') {
				// We're accepting an inbound request
				$inRequest->update(['status' => 'Accepted']);
				Friends::insert(['friend_id' => $friender->id, 'friended_id' => $user_id, 'status' => 'Accepted']);
			} else {
				// We're sending an outbound request
				Friends::insert(['friend_id' => $friender->id, 'friended_id' => $user_id]);
			}
			$this->commitReturn();
		} catch (Exception $e) {
			$this->rollbackReturn();
		}
		return;
	}
	public function unfriendUser($friender, $user_id) {
		\DB::beginTransaction();

		try {
			if($friend = Friends::where('friend_id', $friender->id)->where('friended_id', $user_id)) $friend->delete();
			if($friended = Friends::where('friend_id', $user_id)->where('friended_id', $friender->id)) $friended->delete();

			$this->commitReturn();
		} catch (Exception $e) {
			$this->rollbackReturn();
		}
		return;
	}
}