<?php

namespace App\Services\Forum;

use Exception;

use App\Services\Service;
class ForumBoardService extends Service
{
	public function manageThreadCount($board, $type, $count = 1)
	{
		\DB::beginTransaction();

		try {
			if($type == 'add') $count = $board->thread_count + $count;
			else $count = $board->thread_count - $count;

			$board->update(['thread_count' => $count]);

			return $this->commitReturn();
		} catch (Exception $e) {
			if($e->getMessage()) $this->setError('pets', $e->getMessage());
			else $this->setError('pets', 'Unable to update pet count, please try again.');
		}
		return $this->rollbackReturn();
	}
}