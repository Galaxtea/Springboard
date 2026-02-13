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
		Schema::create('site_news', function (Blueprint $table) {
			$table->foreignId('thread_id');
			$table->text('content_html'); // Since the news post may have a [more] cutoff, we use a special content_html here

			$table->primary('thread_id');
			$table->foreign('thread_id')->references('id')->on('forum_threads');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('site_news');
	}
};
