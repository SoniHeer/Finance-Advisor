<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAiChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ai_chats', function (Blueprint $table) {
            $table->id();

            // 🔗 User reference
            $table->unsignedBigInteger('user_id');

            // 💬 Chat message
            $table->text('message');

            // 🤖 Who sent the message
            $table->enum('sender', ['user', 'ai']);

            $table->timestamps();

            // 🔐 Foreign key
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ai_chats');
    }
}
