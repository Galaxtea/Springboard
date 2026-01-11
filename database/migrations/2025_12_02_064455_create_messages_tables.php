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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->bigInteger('first_message_id')->unsigned();
            $table->bigInteger('last_message_id')->unsigned();
            $table->integer('recipient_count')->unsigned()->default(2);
            $table->bigInteger('message_count')->unsigned()->default(1);
        });

        Schema::create('message_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->bigInteger('parent_folder')->unsigned()->nullable()->default(null);
            $table->string('name');
            $table->bigInteger('message_count')->unsigned()->default(0);
            $table->bigInteger('unread_count')->unsigned()->default(0);


            $table->index('user_id');
            $table->index('parent_folder');
            $table->foreign('user_id')->references('id')->on('users')->index();
        });

        Schema::create('message_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id');
            $table->foreignId('sender_id');
            $table->text('content_html');
            $table->timestamp('created_at')->nullable();
            $table->boolean('staff_anonymous')->default(0);


            $table->index('message_id');
            $table->index('sender_id');
            $table->foreign('message_id')->references('id')->on('messages');
            $table->foreign('sender_id')->references('id')->on('users');
        });

        Schema::create('message_recipients', function (Blueprint $table) {
            $table->foreignId('user_id');
            $table->foreignId('message_id');
            $table->boolean('has_read')->default(0);
            $table->bigInteger('last_read_id')->unsigned()->nullable()->default(null);
            $table->bigInteger('unread_count')->unsigned()->default(1);
            $table->boolean('has_replied')->default(0);
            $table->foreignId('folder_id')->nullable()->default(null);
            $table->softDeletes();


            $table->index('folder_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('message_id')->references('id')->on('messages');
            $table->foreign('folder_id')->references('id')->on('message_folders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_recipients');
        Schema::dropIfExists('message_replies');
        Schema::dropIfExists('message_folders');
        Schema::dropIfExists('messages');
    }
};
