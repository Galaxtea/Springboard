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
		Schema::create('users', function (Blueprint $table) {
			$table->id();
			$table->string('username', 32)->unique();

			$table->bigInteger('rank_id')->unsigned()->default(1);
			$table->boolean('has_upgrades')->default(0);
			$table->boolean('has_bans')->default(0);

			$table->bigInteger('pri_curr')->unsigned()->default(0);
			$table->bigInteger('sec_curr')->unsigned()->default(0);

			$table->timestamp('active_at')->nullable()->default(null)->index();
			$table->rememberToken();
			$table->timestamps();
		});

		Schema::create('user_settings', function (Blueprint $table) {
			$table->foreignId('user_id')->primary()->references('id')->on('users');
			$table->boolean('private_profile')->default(1);

			$table->string('email')->unique();
			$table->timestamp('email_verified_at')->nullable();
			$table->string('password');
			$table->date('birthday');
			$table->string('timezone')->nullable();

			$table->integer('reg_step')->default(0);
			$table->boolean('was_referred')->default(0);

			$table->boolean('display_active')->default(1);
			$table->boolean('allow_messages')->default(1);
			$table->boolean('allow_friends')->default(1);
			$table->string('friend_code', 10)->nullable();
		});

		Schema::create('user_stats', function (Blueprint $table) {
			$table->foreignId('user_id')->primary()->references('id')->on('users');

			$table->bigInteger('invites')->unsigned()->default(0);
			$table->bigInteger('referrals')->unsigned()->default(0);
			// Hi, fill this in with stats :) like pet count and stuff
		});

		Schema::create('user_profiles', function (Blueprint $table) {
			$table->foreignId('user_id')->primary()->references('id')->on('users');

			$table->text('content_bbc')->nullable();
			$table->text('content_html')->nullable();

			$table->boolean('allow_comments')->default(1);
			$table->bigInteger('comment_count')->unsigned()->default(0);
		});

		Schema::create('user_blocks', function (Blueprint $table) {
			$table->foreignId('blocker_id')->references('id')->on('users');
			$table->foreignId('blocked_id')->references('id')->on('users');
			$table->text('self_note')->nullable();

			$table->primary(['blocker_id', 'blocked_id']);
		});

		Schema::create('user_friends', function (Blueprint $table) {
			$table->foreignId('friend_id')->references('id')->on('users');
			$table->foreignId('friended_id')->references('id')->on('users');
			$table->enum('status', ['Pending', 'Accepted'])->default('Pending');    // We don't need rejected; just delete then

			$table->primary(['friend_id', 'friended_id']);
		});

		Schema::create('password_reset_tokens', function (Blueprint $table) {
			$table->string('email')->primary();
			$table->string('token');
			$table->timestamp('created_at')->nullable();
		});

		Schema::create('user_referrals', function (Blueprint $table) {
			$table->foreignId('user_id')->primary()->references('id')->on('users');
			$table->foreignId('referred_by')->index()->references('id')->on('users');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('users');

		Schema::dropIfExists('user_settings');
		Schema::dropIfExists('user_stats');
		Schema::dropIfExists('user_profiles');
		Schema::dropIfExists('user_blocks');
		Schema::dropIfExists('user_friends');
		Schema::dropIfExists('user_referrals');

		Schema::dropIfExists('password_reset_tokens');
	}
};
