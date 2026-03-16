<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ADD is_personal to incomes
        Schema::table('incomes', function (Blueprint $table) {
            if (!Schema::hasColumn('incomes', 'is_personal')) {
                $table->boolean('is_personal')
                      ->default(true)
                      ->after('family_id');
            }
        });

        // ADD is_personal to expenses
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'is_personal')) {
                $table->boolean('is_personal')
                      ->default(true)
                      ->after('family_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            if (Schema::hasColumn('incomes', 'is_personal')) {
                $table->dropColumn('is_personal');
            }
        });

        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'is_personal')) {
                $table->dropColumn('is_personal');
            }
        });
    }
};