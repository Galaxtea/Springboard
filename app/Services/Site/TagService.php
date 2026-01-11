<?php

namespace App\Services\Site;

use DB;
use Exception;
use Auth;
use App\Services\Service;

use App\Models\Site\Tag\ContentTag;

class TagService extends Service
{
	public function get($id) {
		return ContentTag::find($id);
	}

	public function getTagsByType(array $type) {
		$query = ContentTag::usable(parent::$user)->where('type', 'like', "%{$type[0]}%");
		if(($count = count($type)) > 1) {
			for($i = 1; $i < $count; $i++) {
				$query->orWhere('type', 'like', "%{$type[$i]}%");
			}
		}
		return $query->get();
	}
}