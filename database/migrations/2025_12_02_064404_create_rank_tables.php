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
		Schema::create('ranks', function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->string('slug'); //use this in certain checks
			$table->boolean('is_staff')->default(0);
			$table->boolean('is_admin')->default(0);
			$table->string('color', 6);
			$table->text('description');
			$table->integer('power')->default(0); //order of the ranks, 0 being lowest, 100 being highest
		});

		Schema::create('permissions', function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->string('slug'); //use this in checks for route access
			$table->text('description');
		});

		Schema::create('rank_permissions', function (Blueprint $table) {
			$table->foreignId('rank_id');
			$table->foreignId('permission_id');

			$table->primary(['rank_id', 'permission_id']);
			$table->foreign('rank_id')->references('id')->on('ranks');
			$table->foreign('permission_id')->references('id')->on('permissions');
		});

		Schema::table('users', function ($table) {
			$table->foreign('rank_id')->references('id')->on('ranks');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
        try {
            Schema::table('users', function ($table) {
                $table->dropForeign(['rank_id']);
            });
        } catch (Exception $e) {}
        Schema::dropIfExists('rank_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('ranks');
	}
};
