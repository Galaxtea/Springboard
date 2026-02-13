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
		Schema::create('site_settings', function (Blueprint $table) {
			$table->string('ref_key');
			$table->text('value');
			$table->text('text');

			$table->primary('ref_key');
		});

		Schema::create('reg_codes', function (Blueprint $table) {
			$table->id();
			$table->string('token')->unique()->collation('utf8_bin')->index();
			$table->boolean('is_used')->default(0);
			$table->foreignId('maker_id')->unsigned()->nullable()->default(null)->index()->references('id')->on('users');
			$table->foreignId('user_id')->unsigned()->nullable()->default(null)->index()->references('id')->on('users');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('site_settings');
		Schema::dropIfExists('reg_codes');
	}
};
