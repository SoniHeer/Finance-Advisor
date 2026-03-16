<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpiresAtToFamilyInvitesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('family_invites', function (Blueprint $table) {

            // Add expiration column (nullable for backward compatibility)
            if (!Schema::hasColumn('family_invites', 'expires_at')) {
                $table->timestamp('expires_at')
                      ->nullable()
                      ->after('token')
                      ->index(); // performance optimization
            }

            // Ensure token is indexed (for fast lookup)
            $table->index('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('family_invites', function (Blueprint $table) {

            if (Schema::hasColumn('family_invites', 'expires_at')) {
                $table->dropColumn('expires_at');
            }

            $table->dropIndex(['token']);
        });
    }
}
