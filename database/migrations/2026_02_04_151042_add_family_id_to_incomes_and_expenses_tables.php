<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ADD family_id to incomes
        Schema::table('incomes', function (Blueprint $table) {
            if (!Schema::hasColumn('incomes', 'family_id')) {
                $table->unsignedBigInteger('family_id')->nullable()->after('user_id');
            }
        });

        // ADD family_id to expenses
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'family_id')) {
                $table->unsignedBigInteger('family_id')->nullable()->after('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            if (Schema::hasColumn('incomes', 'family_id')) {
                $table->dropColumn('family_id');
            }
        });

        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'family_id')) {
                $table->dropColumn('family_id');
            }
        });
    }
};
