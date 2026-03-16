<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {

            $table->id();

            // Contact Information
            $table->string('name');
            $table->string('email');
            $table->string('subject')->nullable();
            $table->text('message');

            // CRM Features
            $table->boolean('is_read')->default(false);
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();

            // Timestamps
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
