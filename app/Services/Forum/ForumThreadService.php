<?php

namespace App\Services\Forum;

use Exception;
use Illuminate\Support\Arr;

use App\Models\Forum\Thread;
use App\Models\Forum\Post;
use App\Models\Forum\BoardThreads;
use App\Models\Forum\Board;
use App\Models\Site\Tag\ContentTagged;
use App\Services\Forum\ForumPostService as PostService;
use App\Services\Forum\ForumBoardService as BoardService;
use App\Services\Site\SiteNewsService as NewsService;

use App\Services\Service;
class ForumThreadService extends Service
{
	public function create($data) {
		\DB::beginTransaction();

		try {
			$thread_data = Arr::except($data, ['content', 'tags', 'board_id'])+['orig_board_id' => $data['board_id']];
			if($thread = Thread::create($thread_data)) {
				if($data['tags']) {
					foreach($data['tags'] as $tag_id => $tag) {
						$tag_data = [
							'content_type' => 'App\Models\Forum\Thread',
							'content_id' => $thread->id,
							'tag_id' => $tag_id
						];

						ContentTagged::create($tag_data);
					}
				}

				$post_data = [
					'poster_id' => $data['poster_id'],
					'thread_id' => $thread->id,
					'content_bbc' => $data['content_bbc'],
				];
				$post_service = new PostService;
				if($post = $post_service->create($post_data, $thread)) {
					$thread->first_post_id = $post->id;
					$thread->save();

					BoardThreads::create(['thread_id' => $thread->id, 'board_id' => $data['board_id']]);
					if(!(new BoardService)->manageThreadCount($thread->board, 'add')) throw new Exception("Error Processing Request", 1);
					if($thread->board->slug == 'anns') {
						if(!(new NewsService)->manageNews(['thread_id' => $thread->id, 'content_bbc' => $data['content_bbc']])) throw new Exception("Error Processing Request", 1);
					}
				} else {
					if($post_service->hasErrors()) throw new Exception($post_service->getError('content_bbc')[0], 1);
					throw new Exception('Unable to create initial post.');
				}
			} else {
				throw new Exception('Unable to create thread.');
			}

			return $this->commitReturn($thread);
		} catch (Exception $e) {
			if($e->getMessage()) $this->setError('name', $e->getMessage());
			else $this->setError('name', 'Unable to post new thread, please try again.');
		}
		return $this->rollbackReturn();
	}

	public function update($thread, $data) {
		\DB::beginTransaction();
		try {
			$thread->timestamps = false;

			$thread_data = Arr::except($data, ['tags']);
			$thread->update($thread_data);
			$thread->first->update(['poster_id' => $data['poster_id']]);

			$thread->tags()->delete();
			if($data['tags']) {
				foreach($data['tags'] as $tag_id => $tag) {
					$tag_data = [
						'content_type' => 'App\Models\Forum\Thread',
						'content_id' => $thread->id,
						'tag_id' => $tag_id
					];

					ContentTagged::create($tag_data);
				}
			}

			$thread->timestamps = true;
			return $this->commitReturn();
		} catch (Exception $e) {
			if($e->getMessage()) $this->setError('name', $e->getMessage());
			else $this->setError('name', 'Unable to update the thread, please try again.');
		}
		return $this->rollbackReturn();
	}

	public function restore($thread) {
		\DB::beginTransaction();
		try {
			$thread->timestamps = false;

			if(!$thread->restore()) throw new Exception("Unable to restore the thread.", 1);

			$count = count($thread->boards);
			$service = new BoardService;
			for($i = 0; $i < $count; $i++) {
				$service->manageThreadCount($thread->boards[$i], 'add');
			}
			
			$thread->timestamps = true;
			return $this->commitReturn();
		} catch (Exception $e) {
			if($e->getMessage()) $this->setError('thread', $e->getMessage());
			else $this->setError('thread', 'Unable to restore the thread, please try again.');
		}
		return $this->rollbackReturn();
	}

	public function move($thread, $data) {
		\DB::beginTransaction();
		try {
			$thread->timestamps = false;

			if($thread->orig_board_id == $data['board_id']) throw new Exception("The selected board is already the thread's primary board.", 1);

			if(!BoardThreads::where('thread_id', $data['thread_id'])->where('board_id', $data['board_id'])->first()) {
				if(!$this->clone($thread, $data)) throw new Exception("Unable to clone the thread for moving.", 1);
			}
			if(!$this->unclone($thread, $thread->orig_board_id)) throw new Exception("Unable to remove original thread clone for moving.", 1);
			$thread->update(['orig_board_id' => $data['board_id']]);

			$thread->timestamps = true;
			return $this->commitReturn();
		} catch (Exception $e) {
			if($e->getMessage()) $this->setError('thread', $e->getMessage());
			else $this->setError('thread', 'Unable to move the thread, please try again.');
		}
		return $this->rollbackReturn();
	}

	public function clone($thread, $data) {
		\DB::beginTransaction();
		try {
			if(BoardThreads::where('thread_id', $data['thread_id'])->where('board_id', $data['board_id'])->first()) throw new Exception("The thread is already cloned to the selected board.", 1);
			$clone = BoardThreads::create($data);
			if(!$thread->is_deleted) {
				if(!(new BoardService)->manageThreadCount(Board::find($data['board_id']), 'add')) throw new Exception("Error Processing Request", 1);
			}

			return $this->commitReturn($clone);
		} catch (Exception $e) {
			if($e->getMessage()) $this->setError('thread', $e->getMessage());
			else $this->setError('clone', 'Unable to clone the thread, please try again.');
		}
		return $this->rollbackReturn();
	}

	public function unclone($thread, $board_id) {
		\DB::beginTransaction();
		try {
			if(!BoardThreads::where('thread_id', $thread->id)->where('board_id', $board_id)->delete()) throw new Exception("The thread is not in this board's list.", 1);
			if(!$thread->is_deleted) {
				if(!(new BoardService)->manageThreadCount(Board::find($board_id), 'sub')) throw new Exception("Error Processing Request", 1);
			}

			return $this->commitReturn();
		} catch (Exception $e) {
			if($e->getMessage()) $this->setError('thread', $e->getMessage());
			else $this->setError('thread', 'Unable to move the thread, please try again.');
		}
		return $this->rollbackReturn();
	}

	public function touch($thread, $type, $post = null, $count = 1) {
		if(!$post) $post = Post::where('thread_id', $thread->id)->orderBy('created_at', 'DESC')->first();

		\DB::beginTransaction();

		try {
			$thread->timestamps = false;
			$thread->last_post_id = $post->id;

			if($type == 'add') {
				$thread->updated_at = $post->created_at;
				$thread->post_count = $thread->post_count + $count;
			} else $thread->post_count = $thread->post_count - $count;

			$thread->save();
			$thread->timestamps = true;
			return $this->commitReturn();
		} catch (Exception $e) {
			if($e->getMessage()) $this->setError('content_bbc', $e->getMessage());
			else $this->setError('content_bbc', 'Unable to update thread, please try again.');
		}
		return $this->rollbackReturn();
	}

	public function addSub($thread, $user_id) {
		\DB::beginTransaction();
		try {
			$thread->subList()->insert(['thread_id' => $thread->id, 'user_id' => $user_id]);
			$this->commitReturn();
		} catch (Exception $e) {
			$this->rollbackReturn();
		}
		return;
	}
	public function removeSub($sub) {
		\DB::beginTransaction();
		try {
			$sub->delete();
			$this->commitReturn();
		} catch (Exception $e) {
			$this->rollbackReturn();
		}
		return;
	}
}