<?php

namespace App\Services\Forum;

use Exception;

use App\Models\Forum\Board;
use App\Models\Forum\Thread;
use App\Models\Forum\Post;
use App\Models\Forum\PostEdit;

use App\Helpers\BBCodeParser as BBCode;
use App\Helpers\ProfanityFilter as Filter;
use App\Services\Forum\ForumBoardService as BoardService;
use App\Services\Forum\ForumThreadService as ThreadService;

use App\Services\Service;
class ForumPostService extends Service
{
	public function create($data, $thread) {
		\DB::beginTransaction();

		try {
			if($thread->is_locked && !parent::$user->perms('can_msg_mod')) throw new Exception("You do not have permission to make posts on locked threads.", 1);

			if(Filter::filter($data['content_bbc'])) {
				if(Filter::$hit_context) throw new Exception("Please refrain from discussing ".Filter::$hit_context." onsite.");
				if(Filter::$hit_count >= 5) throw new Exception("Please refrain from excessively swearing onsite.");
			}
			$data['content_html'] = BBCode::parse(Filter::$filtered_content);

			$post = Post::create($data);
			if(!(new ThreadService)->touch($thread, 'add', $post)) throw new Exception("Error Processing Request", 1);

			return $this->commitReturn($post);
		} catch (Exception $e) {
			if($e->getMessage()) $this->setError('content_bbc', $e->getMessage());
			else $this->setError('content_bbc', 'Unable to create post, please try again.');
		}
		return $this->rollbackReturn();
	}


	public function update($data, $post) {
		\DB::beginTransaction();

		try {
			if(Filter::filter($data['content_bbc'])) {
				if(Filter::$hit_context) throw new Exception("Please refrain from discussing ".Filter::$hit_context." onsite.");
				if(Filter::$hit_count >= 5) throw new Exception("Please refrain from excessively swearing onsite.");
			}
			$data['content_html'] = BBCode::parse(Filter::$filtered_content);

			$edited = [
				'editor_id' => $data['editor_id'] ? $data['editor_id'] : $post->poster_id,
				'post_id' => $post->id,
				'content_bbc' => $post->content,
				'content_html' => $post->display_content,
				'created_at' => $post->updated_at,
			];


			$post->update($data);
			PostEdit::create($edited);

			return $this->commitReturn($post);
		} catch (Exception $e) {
			if($e->getMessage()) $this->setError('content_bbc', $e->getMessage());
			else $this->setError('content_bbc', 'Unable to edit the post, please try again.');
		}
		return $this->rollbackReturn();
	}


	public function delete($post) {
		\DB::beginTransaction();

		try {
			if($post->id == $post->thread->first_post_id) {
				$post->thread->timestamps = false;

				$post->thread->delete();

				$boards = $post->thread->boards;
				$count = count($boards);
				$board_service = new BoardService;
				for($i = 0; $i < $count; $i++) {
					if(!$board_service->manageThreadCount($boards[$i], 'sub')) throw new Exception("Error Processing Request", 1);
				}

				$post->thread->timestamps = true;
			} else {
				$post->delete();
				if(!(new ThreadService)->touch($post->thread, 'sub')) throw new Exception("Error Processing Request", 1);
			}

			return $this->commitReturn();
		} catch (Exception $e) {}
		return $this->rollbackReturn();
	}
}