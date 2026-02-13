<?php

namespace App\Traits;

trait Reportable
{
	public function reports() {
		return $this->morphMany(Report::class, 'reportable');
	}
}
