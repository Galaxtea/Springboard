<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('reports', function (Blueprint $table) {
			$table->id();
			$table->foreignId('reporter_id')->nullable()->default(null);
			$table->text('report_reason');
			$table->text('report_description');
			$table->morphs('reportable');
			$table->string('status');
			$table->foreignId('claimed_by')->nullable()->default(null);
			$table->text('action_taken')->nullable()->default(null);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('reports');
	}
};
