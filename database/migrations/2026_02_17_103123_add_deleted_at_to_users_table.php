<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds soft delete support to users table.
     *
     * @return void
     */
    public function up()
    {
        // Safety check (prevents duplicate column error)
        if (!Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->softDeletes()->after('remember_token');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * Removes soft delete column safely.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
}
