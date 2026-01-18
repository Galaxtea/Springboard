<?php

namespace App\Http\Controllers\Forum;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

use App\Validators\Forum\ThreadValidator;
use App\Services\Forum\ForumService;
use App\Services\Forum\ForumThreadService;
use App\Services\Site\TagService;

use App\Http\Controllers\Controller;
class ThreadController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(ForumService $service, TagService $tag_service) {
		// $this->middleware('auth')->except('index');
		$this->service = $service;
		$this->tag_service = $tag_service;
	}

	public function getThread($board, $thread_id) {
		$board = $this->service->getBoard($board);

		if(parent::$is_auth && parent::$user->perms('can_panel')) {
			$thread = $this->service->getThread($thread_id, true);
			$posts = $thread->posts()->withTrashed();
		} else {
			$thread = $this->service->getThread($thread_id);
			$posts = $thread->posts();
		}

		if(!$board || !$board->checkPerms(['can_read']) || !$thread) abort(404);

		return view('forums.thread', ['thread' => $thread, 'board' => $board, 'posts' => $posts->paginate()]);
	}

	public function getNew($slug) {
		$board = $this->service->getBoard($slug);

		if(!$board || !$board->checkPerms(['can_read', 'can_new'])) abort(404);
		$tags = $this->tag_service->getTagsByType($board->taggable);

		return view('forums.thread_edit', ['board' => $board, 'tags' => $tags]);
	}

	public function postNew(Request $request, ThreadValidator $validator, ForumThreadService $service, $slug) {
		$board = $this->service->getBoard($slug);

		if(!parent::$is_auth || !$board || !$board->checkPerms(['can_read', 'can_new'])) return redirect()->to('/forums/'.$slug);

		$errors = new MessageBag();

		$extra = ['is_sticky' => $request->has('is_sticky'), 'is_locked' => $request->has('is_locked'), 'tags' => $request->has('tags') ? $request['tags'] : null, 'board_id' => $board->id, 'poster_id' => parent::$user->id];
		if(!parent::$user->perms('can_msg_mod')) {
			$extra['is_sticky'] = 0;
			$extra['is_locked'] = 0;
		}

		if(!$validator->validate($request->only(['name', 'content_bbc']) + $extra, 'create')) {
			$errors->merge($validator->errors()->toArray());
		} elseif(!$thread = $service->create($validator->data())) {
			$errors->merge($service->errors()->toArray());
		} else {
			return redirect()->to('/forums/'.$slug.'/'.$thread->id);
		}

		return redirect()->to('/forums/'.$slug.'/new')->withInput()->withErrors($errors);
	}



	public function getManage($thread_id) {
		$thread = $this->service->getThread($thread_id, true);

		if(!$thread) abort(404);
		$tags = $this->tag_service->getTagsByType(['forums']);
		$boards = $this->service->getBoardSelect();

		return view('forums.thread_manage', ['thread' => $thread, 'tags' => $tags, 'boards' => $boards]);
	}

	public function postEdit(Request $request, ThreadValidator $validator, ForumThreadService $service, $thread_id) {
		$thread = $this->service->getThread($thread_id, true);
		if(!$thread || $request->missing('poster')) return redirect()->to('/forums/manage/'.$thread_id);

		$errors = new MessageBag();

		$poster_id = explode(' - ', $request['poster'])[0];
		$extra = ['is_sticky' => $request->has('is_sticky'), 'is_locked' => $request->has('is_locked'), 'tags' => $request->has('tags') ? $request['tags'] : null, 'poster_id' => $poster_id];

		if(!$validator->validate($request->only(['name']) + $extra, 'update')) {
			$errors->merge($validator->errors()->toArray());
		} elseif(!$service->update($thread, $validator->data())) {
			$errors->merge($service->errors()->toArray());
		} else {
			return redirect()->to('/forums/manage/'.$thread_id)->with('success', "The thread has been updated successfully.");
		}
		return redirect()->to('/forums/manage/'.$thread_id)->withErrors($errors);
	}

	public function postRestore(Request $request, ForumThreadService $service, $thread_id) {
		$thread = $this->service->getThread($thread_id, true);
		if(!$thread) return redirect()->to('/forums/manage/'.$thread_id);

		$errors = new MessageBag();
		if(!$service->restore($thread)) {
			$errors->merge($service->errors()->toArray());
		}

		return redirect()->to('/forums/manage/'.$thread_id)->withErrors($errors);
	}

	public function postMove(Request $request, ThreadValidator $validator, ForumThreadService $service, $thread_id) {
		$thread = $this->service->getThread($thread_id, true);
		if(!$thread) return redirect()->to('/forums/manage/'.$thread_id);

		$errors = new MessageBag();

		if(!$validator->validate(['thread_id' => $thread->id, 'board_id' => $request['board']], 'clone')) {
			$errors->merge($validator->errors()->toArray());
		} elseif(!$service->move($thread, $validator->data())) {
			$errors->merge($service->errors()->toArray());
		} else {
			return redirect()->to('/forums/manage/'.$thread_id)->with('success', "The thread has been moved successfully.");
		}
		return redirect()->to('/forums/manage/'.$thread_id)->withErrors($errors);
	}

	public function postClone(Request $request, ThreadValidator $validator, ForumThreadService $service, $thread_id) {
		$thread = $this->service->getThread($thread_id, true);
		if(!$thread) return redirect()->to('/forums/manage/'.$thread_id);

		$errors = new MessageBag();

		if(!$validator->validate(['thread_id' => $thread->id, 'board_id' => $request['clone']], 'clone')) {
			$errors->merge($validator->errors()->toArray());
		} elseif(!$service->clone($thread, $validator->data())) {
			$errors->merge($service->errors()->toArray());
		} else {
			return redirect()->to('/forums/manage/'.$thread_id)->with('success', "The thread has been cloned successfully.");
		}
		return redirect()->to('/forums/manage/'.$thread_id)->withErrors($errors);
	}

	public function postUnclone(Request $request, ForumThreadService $service, $thread_id) {
		$thread = $this->service->getThread($thread_id, true);
		if(!$thread || $request->missing('remove')) return redirect()->to('/forums/manage/'.$thread_id);

		$errors = new MessageBag();

		if(!$service->unclone($thread, $request['remove'])) {
			$errors->merge($service->errors()->toArray());
		} else {
			return redirect()->to('/forums/manage/'.$thread_id)->with('success', "The thread has been uncloned successfully.");
		}
		return redirect()->to('/forums/manage/'.$thread_id)->withErrors($errors);
	}




	public function addSub(ForumThreadService $service, $thread_id) {
		if(!parent::$is_auth) return abort(404, 'You need to be logged in to do that.');
		$thread = $this->service->getThread($thread_id);
		if(!$thread) return back();

		$subbed = $thread->subList()->where('user_id', parent::$user->id)->first();

		if(!$subbed) $service->addSub($thread, parent::$user->id);

		return back();
	}
	public function removeSub(ForumThreadService $service, $thread_id) {
		if(!parent::$is_auth) return abort(404, 'You need to be logged in to do that.');
		$thread = $this->service->getThread($thread_id);
		if(!$thread) return back();

		$subbed = $thread->subList()->where('user_id', parent::$user->id);

		if($subbed) $service->removeSub($subbed);

		return back();
	}
}
