<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, update all empty strings to null
        DB::table('companies')
            ->where('figi_code', '')
            ->update(['figi_code' => null]);

        // Drop the existing unique constraint
        Schema::table('companies', function (Blueprint $table) {
            $table->dropUnique('companies_figi_code_unique');
        });

        // Add unique constraint that allows multiple nulls
        DB::statement('ALTER TABLE companies ADD UNIQUE KEY companies_figi_code_unique (figi_code)');
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropUnique('companies_figi_code_unique');
            $table->string('figi_code', 20)->nullable()->unique()->change();
        });
    }
};
