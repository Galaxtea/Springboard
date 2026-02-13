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
		Schema::create('word_lists', function (Blueprint $table) {
			$table->id();
			$table->string('word');
			$table->string('filter_type');
			$table->text('regex');
			$table->integer('handle_hit')->unsigned()->default(1);
			$table->string('endings')->nullable()->default(null);
		});

		Schema::create('word_letter_subs', function (Blueprint $table) {
			$table->string('letter')->unique()->primary();
			$table->text('subs');
			$table->string('regex');
		});


		Schema::create('word_contexts', function (Blueprint $table) {
			$table->id();
			$table->string('context');
			$table->text('words');
			$table->text('regex');
		});

		Schema::create('word_context_blocks', function (Blueprint $table) {
			$table->id();
			$table->string('nickname');
			$table->text('contexts');
			$table->integer('handle_hit')->unsigned()->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('word_lists');
		Schema::dropIfExists('word_letter_subs');
		Schema::dropIfExists('word_contexts');
		Schema::dropIfExists('word_context_blocks');
	}
};
