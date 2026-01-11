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
        Schema::create('content_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('display_name');
            $table->string('type')->default('anything');
            $table->string('req_perm')->nullable()->default(null);
            $table->string('req_rank')->nullable()->default(null);
            $table->string('color')->nullable()->default(null);
            $table->text('description');
        });

        Schema::create('content_tagged', function (Blueprint $table) {
            $table->morphs('content');
            $table->foreignId('tag_id');

            $table->primary(['content_type', 'content_id', 'tag_id']);
            $table->foreign('tag_id')->references('id')->on('content_tags');
        });

        Schema::create('user_tag_blocks', function (Blueprint $table) {
            $table->foreignId('user_id');
            $table->foreignId('tag_id');

            $table->primary(['user_id', 'tag_id']);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('tag_id')->references('id')->on('content_tags');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_tag_blocks');
        Schema::dropIfExists('content_tagged');
        Schema::dropIfExists('content_tags');
    }
};
