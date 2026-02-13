<?php

namespace App\Http\Controllers\Forum;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

use App\Validators\Forum\ThreadValidator;
use App\Services\Forum\ForumService;
use App\Services\Forum\ForumPostService;
use App\Helpers\BBCodeParser as BBCode;

use App\Http\Controllers\Controller;
class PostController extends Controller
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

	public function locatePost($board, $thread_id, $id) {
		$post = $this->service->getPost($id);
		if($post?->thread_id != $thread_id) return redirect()->to('/forums');

		// Find the post's thread
		$posts = $post->thread->posts()->where('id', '<=', $id);
		if(auth()->user()?->perms('can_msg_mod')) $posts = $posts->withTrashed();

		// Find which page the post would end up on
		$found_page = $posts->paginate()->lastPage();

		return redirect()->to("/forums/{$post->board->slug}/{$post->thread_id}?page={$found_page}#post_{$id}");
	}

	public function postNew(Request $request, ThreadValidator $validator, ForumPostService $service, $id) {
		$thread = $this->service->getThread($id);

		if(!auth()->user() || !$thread || !$thread->board->checkPerms(['can_read', 'can_post'])) return redirect()->to('/forums');

		$errors = new MessageBag();

		if(!$validator->validate(['poster_id' => auth()->user()?->id, 'thread_id' => $id, 'content_bbc' => $request['content_bbc']], 'post')) {
			$errors->merge($validator->errors()->toArray());
		} else if(!$post = $service->create($validator->data(), $thread)) {
			$errors->merge($service->errors()->toArray());
		} else {
			$pages = $thread->posts()->paginate();
			return redirect()->to('forums/'.$thread->board->slug.'/'.$thread->id.'?page='.$pages->lastPage().'#post_'.$post->id);
		}

		return redirect()->to('forums/'.$thread->board->slug.'/'.$thread->id)->withInput()->withErrors($errors);
	}

	public function postPreview(Request $request) {
		return BBCode::parse($request['content_bbc']);
	}


	public function getEdit($id) {
		$post = $this->service->getPost($id);

		if(!auth()->user() || !$post || $post->thread->is_deleted || !$post->board->checkPerms(['can_read', 'can_post'])) return redirect()->to('/forums');
		if($post->is_deleted || (auth()->user()?->id != $post->poster_id && !auth()->user()?->perms('can_msg_mod'))) return redirect()->to("/forums/{$post->board->slug}/{$post->thread_id}");

		return view('forums.post_edit', ['post' => $post]);
	}

	public function postEdit(Request $request, ThreadValidator $validator, ForumPostService $service, $id) {
		$post = $this->service->getPost($id);

		if(!auth()->user() || !$post || $post->thread->is_deleted || !$post->board->checkPerms(['can_read', 'can_post'])) return redirect()->to('/forums');
		if($post->is_deleted || (auth()->user()?->id != $post->poster_id && !auth()->user()?->perms('can_msg_mod'))) return redirect()->to("/forums/{$post->board->slug}/{$post->thread_id}");

		$errors = new MessageBag();

		if(!$validator->validate($request->only(['post_id', 'content_bbc']) + ['editor_id' => auth()->user()?->id], 'post_edit')) {
			$errors->merge($validator->errors()->toArray());
		} else if(!$service->update($validator->data(), $post)) {
			$errors->merge($service->errors()->toArray());
		} else {
			return redirect()->to("/forums/{$post->board->slug}/{$post->thread_id}");
		}

		return redirect()->to('forums/post/'.$post->id.'/edit')->withInput()->withErrors($errors);
	}


	public function postDelete(Request $request, ForumPostService $service, $id) {
		$post = $this->service->getPost($id);

		if(!$post || (auth()->user()?->id != $post->poster_id && !auth()->user()?->perms('can_msg_mod'))) return false;

		if(!$service->delete($post)) {
			$return = ['errors' => ['Unable to delete post.'], 'success' => false];
		} else if($post->id == $post->thread->first_post_id) {
			$return = ['success' => true, 'redirect' => true];
		} else $return = ['success' => true];

		if($request->has('js')) return $return;
		else return redirect()->to('forums/manage/'.$post->thread->id);
	}


	public function getHistory($id) {
		$post = $this->service->getPost($id);

		if(!$post || !auth()->user()?->perms('can_msg_mod')) return abort(404);

		return view('forums.post_history', ['post' => $post, 'edits' => $post->edits()->orderBy('id', 'DESC')->paginate()]);
	}
}
