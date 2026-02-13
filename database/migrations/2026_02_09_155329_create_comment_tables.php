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
		Schema::create('comments', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id');
			$table->morphs('commentable');
			$table->text('content_bbc');
			$table->text('content_html');
			$table->timestamps();
			$table->softDeletes();


			$table->index('user_id');
			$table->foreign('user_id')->references('id')->on('users');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('comments');
	}
};
