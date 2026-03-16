<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            // ✅ SIMPLE + SAFE (NO change(), NO hasColumn())
            $table->unsignedTinyInteger('month')->nullable()->after('amount');
            $table->unsignedSmallInteger('year')->nullable()->after('month');
        });
    }

    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropColumn(['month', 'year']);
        });
    }
};
