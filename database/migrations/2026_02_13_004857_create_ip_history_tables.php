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
		Schema::create('ip_history', function (Blueprint $table) {
			$table->foreignId('user_id');
			$table->ipAddress('ip_address');
			$table->timestamps();

			$table->primary(['user_id', 'ip_address']);
			$table->foreign('user_id')->references('id')->on('users');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('ip_history');
	}
};
