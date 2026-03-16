<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFamilyInvitesTable extends Migration
{
    public function up(): void
    {
        Schema::create('family_invites', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Core Relations
            |--------------------------------------------------------------------------
            */

            $table->foreignId('family_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Invite Details
            |--------------------------------------------------------------------------
            */

            // Nullable → for link-based invites
            $table->string('email')->nullable()->index();

            // Secure unique token
            $table->string('token', 64)->unique();

            /*
            |--------------------------------------------------------------------------
            | Invite State
            |--------------------------------------------------------------------------
            */

            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('accepted_at')->nullable()->index();

            // Who accepted (optional tracking)
            $table->foreignId('accepted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Meta
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Performance Indexing
            |--------------------------------------------------------------------------
            */

            $table->index(['family_id', 'accepted_at']);
            $table->index(['family_id', 'expires_at']);

            /*
            |--------------------------------------------------------------------------
            | Prevent duplicate active invites per email
            |--------------------------------------------------------------------------
            */

            $table->unique(['family_id', 'email', 'accepted_at'], 
                'family_invites_unique_active_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_invites');
    }
}
