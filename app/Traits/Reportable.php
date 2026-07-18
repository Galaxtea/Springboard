<?php

namespace App\Traits;

use App\Models\Admin\MainReport;

trait Reportable
{
	public function reports() {
		return $this->morphMany(MainReport::class, 'reportable');
	}
	public function getReportURLAttribute() {
		return "/report/new?type={$this->report_type}&id={$this->id}";
	}
}
