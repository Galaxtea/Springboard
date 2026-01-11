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
        Schema::create('word_blacklist', function (Blueprint $table) {
            $table->id();
            $table->string('word');
            $table->string('filter_type');
            $table->text('subbed');
        });

        Schema::create('word_whitelist', function (Blueprint $table) {
            $table->id();
            $table->string('word');
        });

        Schema::create('word_letter_subs', function (Blueprint $table) {
            $table->string('letter')->unique()->primary();
            $table->text('subs');
            $table->string('regex');
        });

        Schema::create('word_context', function (Blueprint $table) {
            $table->id();
            $table->string('context');
            $table->text('words');
            $table->text('subbed');
        });

        Schema::create('word_context_block', function (Blueprint $table) {
            $table->id();
            $table->string('nickname');
            $table->text('contexts');
        });

        Schema::create('word_report', function (Blueprint $table) {
            $table->id();
            $table->string('hit_content');
            $table->string('hit_as');
            $table->morphs('source');
            $table->string('review_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('word_blacklist');
        Schema::dropIfExists('word_whitelist');
        Schema::dropIfExists('word_letter_sub');
        Schema::dropIfExists('word_context_block');
        Schema::dropIfExists('word_report');
    }
};
