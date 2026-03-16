<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        // ✅ Check FIRST (outside Schema::table)
        if (!Schema::hasColumn('expenses', 'family_id')) {

            Schema::table('expenses', function (Blueprint $table) {
                $table->foreignId('family_id')
                      ->nullable()
                      ->after('user_id')
                      ->constrained()     // defaults to families
                      ->nullOnDelete();   // keep expense if family deleted
            });

        }
    }

    public function down(): void
    {
        // ✅ Safe rollback
        if (Schema::hasColumn('expenses', 'family_id')) {

            Schema::table('expenses', function (Blueprint $table) {
                $table->dropConstrainedForeignId('family_id');
            });

        }
    }
};