<?php

namespace App\Validators\Forum;

use Carbon\Carbon;

use App\Validators\Validator;
class ThreadValidator extends Validator
{
	protected $rules = [
		'create' => [
			'board_id' => 'required|exists:forum_boards,id',
			'poster_id' => 'required|exists:users,id',
			'name' => 'required|string|min:2|max:32',
			'content_bbc' => 'required',
			'is_sticky' => 'nullable|boolean',
			'is_locked' => 'nullable|boolean',
			'tags' => 'nullable|array',
			'tags.*' => 'required_with:tags|boolean',
		],

		'clone' => [
			'thread_id' => 'required|exists:forum_threads,id',
			'board_id' => 'required|exists:forum_boards,id',
		],

		'post' => [
			'thread_id' => 'required|exists:forum_threads,id',
			'poster_id' => 'required|exists:users,id',
			'content_bbc' => 'required',
		],

		'post_edit' => [
			'post_id' => 'required|exists:forum_posts,id',
			'content_bbc' => 'required',
			'editor_id' => 'required|exists:users,id',
		],

		'update' => [
			'name' => 'required|string|min:2|max:32',
			'poster_id' => 'required|exists:users,id',
			'is_sticky' => 'nullable|boolean',
			'is_locked' => 'nullable|boolean',
			'tags' => 'nullable|array',
			'tags.*' => 'required_with:tags|boolean',
		],
	];

	protected $sanitizers = [
		'name',
	];



	public function sanitizeName($data) {
		$data['name'] = trim($data['name']);
		return $data;
	}
}