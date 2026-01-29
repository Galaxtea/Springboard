<?php

namespace App\Services\Forum;

use Exception;

use App\Models\Forum\Forum;
use App\Models\Forum\Board;
use App\Models\Forum\Thread;
use App\Models\Forum\Post;

use App\Services\Service;
class ForumService extends Service
{
	public function getForums(array $data = []) {
		$boards = Board::select('*', 'forum_boards.id as id', 'forum_boards.name as name', 'forum_boards.description as description', 'forums.name as cat_name');

		if(array_key_exists('parent_board', $data)) $boards->where('parent_board', $data['parent_board']);
		if(!array_key_exists('user', $data)) $boards->where('is_public', 1);
		else {
			if(!$data['user']->perms('can_reports')) { // Use this section if there's any boards locked off by an account setting, so that staff still see
				// $boards->where(function($query) use ($data) {
				// 	$query->where('clan_id', $data['user']->clan_id)->orWhereNull('clan_id');
				// });
			}
			if(!$data['user']->perms('forum_boost')) {
				$boards->where('can_read', 1);
			}
		}

		$boards->join('forums', 'forums.id', '=', 'forum_boards.category');

		return $boards->orderBy('forums.sort')->orderBy('forum_boards.sort')->get();
	}

	public function getBoard($slug) {
		$board = Board::where('slug', $slug)->first();

		if(!$board) return abort(404, "There doesn't appear to be a forum board here.");
		return $board;
	}
	public function getBoards() {
		return Board::orderBy('category')->orderBy('sort')->get();
	}
	public function getBoardSelect() {
		return Board::select('id', 'name')->orderBy('name')->get()->mapWithKeys(function($board) {
			return [ $board['id'] => $board['name'] ];
		})->toArray();
	}

	public function getThread($id, $deleted = false) {
		if($deleted) return Thread::withTrashed()->where('id', $id)->with('boards')->first();
		return Thread::where('id', $id)->with('boards')->first();
	}

	public function getPost($id) {
		return Post::withTrashed()->where('id', $id)->first();
	}

	public function getLatest($board_id, $count) {
		return Thread::where('board_id', $board_id)->orderBy('first_post_id', 'DESC')->limit($count)->get();
	}
}