<?php

namespace App\Http\Controllers;

use Config;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use App\Models\Admin\MainReport as Report;
use App\Models\Admin\ReportCategories;


use App\Models\User\User;
use App\Models\Forum\Post;


use App\Http\Controllers\Controller;
class ReportController extends Controller
{
	public function index() {
		// Show the user's list of submitted reports
		$reports = Report::where('reporter_id', auth()->user()->id)->get();
		return view('reports.list', ['reports' => $reports]);
	}


	public function getReport($id) {
		$report = Report::find($id);
		if($report->reporter_id != auth()->user()->id && !auth()->user()->perms('can_reports')) abort(404);
		return view('reports.view', ['report' => $report]);
	}


	public function getNew(Request $request) {
		// We can probably cache forever this part...
		$categories = [];
		ReportCategories::get()->groupBy('main_cat')->map(function($value, $key) use (&$categories) {
			$categories['main_cats'][] = $key;
			$main_subs = [];
			$value->map(function($data) use (&$categories, &$main_subs) {
				$categories['sub_cats'][$data['id']] = $data['sub_cat'];
				$main_subs[] = $data['id'];
			});
			$categories['main_subs'][] = $main_subs;
		});


		// If the user clicked the Report button on something (instead of going to the Report page directly) this
		// will help jump to relevant "Report Type" and give the filed Report a link back to the content itself.
		$main_cat = $request->input('type');
		switch ($main_cat) {
			case 'user':
				$main_cat = 0;
				break;

			case 'forum_post':
				$main_cat = 2;
				break;

			default:
				$main_cat = null;
				break;
		}
		$query_string = "?type={$request['type']}&id={$request['id']}";


		return view('reports.create', ['categories' => $categories, 'main_cat' => $main_cat, 'query' => $query_string]);
	}


	public function postNew(Request $request) {
		// Main report info (we'll want to run this through a Validator)
		$report = [
			'reporter_id' => auth()->user()->id,
			'category' => $request['sub_cat'],
			'title' => $request['title'],
			'content' => $request['content'],
		];


		// If they followed a Report link (see getNew function above ^), this is where that will get fully used.
		$type = $request['type'];
		$id = $request['id'];
		$reportable = null;
		if($id) {
			switch ($type) {
				case 'user':
					$reportable = User::find($id);
					$report['reported_id'] = $id;
					break;

				case 'forum_post':
					$reportable = Post::find($id);
					$report['reported_id'] = $reportable->poster_id;
					break;

				default: break;
			}
		}

		if($reportable) {
			$filed = $reportable->reports()->create($report);
		} else {
			$filed = Report::create($report);
		}


		return redirect()->to('/report/'.$filed->id);
	}
}
