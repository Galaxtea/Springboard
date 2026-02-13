<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Http\Controllers\Controller;
class AdminController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct() {
		Gate::authorize('can-panel');
	}




	public function index(Request $request) {
		return view('admin.index');
	}
}
