<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('incomes', function (Blueprint $table) {

            // ✅ Add column ONLY if it doesn't exist
            if (!Schema::hasColumn('incomes', 'family_id')) {

                $table->foreignId('family_id')
                      ->nullable()
                      ->after('user_id')
                      ->constrained('families')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {

            // ✅ Drop FK + column ONLY if exists
            if (Schema::hasColumn('incomes', 'family_id')) {
                $table->dropConstrainedForeignId('family_id');
            }
        });
    }
};