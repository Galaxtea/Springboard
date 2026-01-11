<?php

namespace App\Services\Site;

use DB;
use Exception;
use App\Services\Service;

use App\Models\Site\SiteNews;
use App\Helpers\BBCodeParser as BBCode;

class SiteNewsService extends Service
{
	public function getLatest() {
		return SiteNews::orderBy('thread_id', 'DESC')->paginate();
	}


	public function manageNews(array $data) {
		$post_data = ['thread_id' => $data['thread_id']];
		$post_data['content_html'] = BBCode::parse(explode('[more]', $data['content_bbc'])[0]);

		if(!$news = SiteNews::find($data['thread_id'])) return SiteNews::create($post_data);
		return $news->update($post_data);
	}
}