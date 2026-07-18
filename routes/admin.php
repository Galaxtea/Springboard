<?php # Admin Panel Routes

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;


Route::controller(AdminController::class)->prefix('panel')->group(function () {
	Route::get('/', 'index')->name('admin');
	// site settings (look into updating the Config file instead of DB)
	// word list


	// user list - search
	Route::controller(UserController::class)->group(function () {
		Route::get('/users', 'getUserList')->name('admin.user_list');
		Route::get('/user/{id}', 'getUser')->name('admin.user');
		Route::get('/user/{id}/ips', 'getUserIPs')->name('admin.user.ips');
		Route::get('/ip/{ip}', 'getIPUsers')->name('admin.ip.users');
	});
	// user page - account info (username, email, reg date, etc.) w/ links to further details
	// user activity
	// user IPs
	// user forum / PM / comment activity


	// Move this to the reports.php route!!!
	// reports - unclaimed / claimed & open / resolved
	Route::controller(ReportController::class)->group(function () {
		Route::get('/reports', 'getReportList')->name('admin.reports.list');
		Route::get('/report/{id}', 'getReport')->name('admin.reports.view');
	});
	// report page - status, comments, claiming staff, report discussion, report content link
});