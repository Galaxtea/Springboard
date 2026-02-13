<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
	// Traits


	// Model Settings
		protected $table = 'reports';
		public $timestamps = true;

		protected $fillable = [
			'reporter_id', 'report_reason', 'report_description', 'status', 'claimed_by', 'action_taken',
			'reportable_type', 'reportable_id',
		];


	// Mutators


	// Accessors


	// Relations
		public function reportable() {
			return $this->morphTo();
		}


	// Functions
}
