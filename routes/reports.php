<?php

use App\Http\Controllers\ReportController;


Route::controller(ReportController::class)->group(function() {
	Route::get('/reports', 'index')->name('reports.list');

	Route::get('/report/new', 'getNew')->name('reports.create');
	Route::post('/report/new', 'postNew')->name('reports.submit');

	Route::get('/report/{id}', 'getReport')->name('reports.view');
});