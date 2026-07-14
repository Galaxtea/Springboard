<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ReportCategories extends Model
{
	// Traits


	// Model Settings
		protected $table = 'report_categories';
		public $timestamps = false;

		protected $fillable = [
			'main_cat', 'sub_cat', 'perm_req',
		];


	// Mutators


	// Accessors


	// Relations


	// Functions
}
