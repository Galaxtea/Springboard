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
		Schema::create('report_categories', function (Blueprint $table) {
			$table->id();
			$table->string('main_cat');
			$table->string('sub_cat');
			$table->string('perm_req');
		});

		Schema::create('reports', function (Blueprint $table) {
			$table->id();
			$table->foreignId('reporter_id')->nullable()->default(null);
			$table->foreignId('reported_id')->nullable()->default(null);
			$table->foreignId('category');
			$table->string('title');
			$table->text('content');
			$table->nullableMorphs('reportable');
			$table->string('status')->default('open');
			$table->foreignId('claimed_by')->nullable()->default(null);
			$table->text('action_taken')->nullable()->default(null);
			$table->timestamps();

			$table->foreign('reporter_id')->references('id')->on('users');
			$table->foreign('reported_id')->references('id')->on('users');
			$table->foreign('category')->references('id')->on('report_categories');
			$table->foreign('claimed_by')->references('id')->on('users');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('report_categories');
		Schema::dropIfExists('reports');
	}
};
