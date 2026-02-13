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
		Schema::create('forums', function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->text('description');
			$table->bigInteger('sort')->unsigned()->default(0)->index();
		});

		Schema::create('forum_boards', function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->string('slug');
			$table->text('description');
			$table->foreignId('category')->nullable()->default(null);
			$table->bigInteger('parent_board')->unsigned()->nullable()->default(null);
			$table->bigInteger('thread_count')->unsigned()->default(0);

			$table->bigInteger('sort')->unsigned()->default(0)->index();
			$table->boolean('is_public')->default(1); // can logged out visitors see it
			$table->text('taggable_type')->nullable()->default(null);
			$table->text('icon')->nullable()->default(null);

			// whether or not the permission requires the forum power perm
			$table->boolean('can_read')->default(0);
			$table->boolean('can_post')->default(0);
			$table->boolean('can_new')->default(0);


			$table->index('slug');
			$table->index('category');
			$table->foreign('category')->references('id')->on('forums');
		});

		Schema::create('forum_threads', function (Blueprint $table) {
			$table->id();

			$table->string('name');
			$table->foreignId('poster_id');
			$table->foreignId('orig_board_id');
			$table->bigInteger('first_post_id')->unsigned()->nullable()->default(null);
			$table->boolean('is_sticky')->default(0);
			$table->boolean('is_locked')->default(0);

			$table->bigInteger('last_post_id')->unsigned()->nullable()->default(null)->index();
			$table->bigInteger('post_count')->unsigned()->default(1);
			$table->timestamps();
			$table->softDeletes();


			$table->index('poster_id');
			$table->index('orig_board_id');
			$table->foreign('poster_id')->references('id')->on('users');
			$table->foreign('orig_board_id')->references('id')->on('forum_boards');
		});

		Schema::create('forum_board_threads', function (Blueprint $table) {
			$table->foreignId('thread_id');
			$table->foreignId('board_id');

			$table->primary(['thread_id', 'board_id']);
			$table->foreign('thread_id')->references('id')->on('forum_threads');
			$table->foreign('board_id')->references('id')->on('forum_boards');
		});

		Schema::create('forum_thread_subs', function (Blueprint $table) {
			$table->foreignId('user_id');
			$table->foreignId('thread_id');

			$table->primary(['user_id', 'thread_id']);
			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('thread_id')->references('id')->on('forum_threads')->onDelete('cascade');
		});

		Schema::create('forum_posts', function (Blueprint $table) {
			$table->id();
			$table->foreignId('poster_id');
			$table->foreignId('editor_id')->nullable()->default(null);
			$table->foreignId('thread_id');
			$table->text('content_bbc');
			$table->text('content_html');
			$table->timestamps();
			$table->softDeletes();


			$table->index('poster_id');
			$table->index('thread_id');
			$table->foreign('poster_id')->references('id')->on('users')->onDelete('cascade');
			$table->foreign('editor_id')->references('id')->on('users')->onDelete('cascade');
			$table->foreign('thread_id')->references('id')->on('forum_threads')->onDelete('cascade');
		});

		Schema::table('forum_threads', function ($table) {
			$table->foreign('first_post_id')->references('id')->on('forum_posts')->onDelete('cascade');
		});

		Schema::create('forum_post_edits', function (Blueprint $table) {
			$table->id();
			$table->foreignId('editor_id');
			$table->foreignId('post_id');
			$table->text('content_bbc');
			$table->text('content_html');
			$table->timestamp('created_at');


			$table->index('post_id');
			$table->foreign('editor_id')->references('id')->on('users');
			$table->foreign('post_id')->references('id')->on('forum_posts')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		try {
			Schema::table('forum_threads', function ($table) {
				$table->dropForeign(['first_post_id']);
			});
		} catch (Exception $e) {}
		Schema::dropIfExists('forum_post_edits');
		Schema::dropIfExists('forum_posts');
		Schema::dropIfExists('forum_thread_subs');
		Schema::dropIfExists('forum_board_threads');
		Schema::dropIfExists('forum_threads');
		Schema::dropIfExists('forum_boards');
		Schema::dropIfExists('forums');
	}
};
