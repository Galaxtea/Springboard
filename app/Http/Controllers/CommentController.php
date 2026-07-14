<?php

namespace App\Http\Controllers;

use Config;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

use App\Models\User\User;
use App\Models\Admin\MainReport;
use App\Models\Admin\PanelReport;

use App\Helpers\BBCodeParser as BBCode;
use App\Helpers\ProfanityFilter as Filter;

use App\Http\Controllers\Controller;
class CommentController extends Controller
{
	public function postUser(Request $request, $id) { // This was the OG code and can be removed once rewritten
		Filter::filter($request['content_bbc']);
		User::find($id)->profile->comments()->create([
			'user_id' => auth()->user()->id,
			'content_bbc' => $request['content_bbc'],
			'content_html' => BBCode::parse(Filter::$filtered_content),
		]);
		return back();
	}



	public function postComment(Request $request, $type, $id) {
		// Make sure you run this through a validator first!
		// And then move this to a service probably


		// Filter the comment first
		Filter::filter($request['content_bbc']);


		// Prep the comment data
		$is_panel = $request->has('is_panel');
		$data = [
			'user_id' => auth()->user()->id,
			'content_bbc' => $request['content_bbc'],
			'content_html' => BBCode::parse(Filter::$filtered_content),
			'is_panel' => $is_panel,
		];


		switch ($type) {
			case 'user':
				$profile = User::find($id)->profile;
				if($profile->allow_comments || $is_panel) {
					$profile->comments()->create($data);
					if(!$is_panel) $profile->increment('comment_count');
				}
				break;

			case 'report':
				MainReport::find($id)->comments()->create($data);
				break;

			case 'panelreport':
				PanelReport::find($id)->comments()->create($data);
				break;

			default:
				// code...
				break;
		}


		return back();
	}
}
