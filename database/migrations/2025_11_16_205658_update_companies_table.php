<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('currency', 10)->nullable()->after('symbol');
            $table->string('exchange')->nullable()->after('currency');
            $table->string('mic_code', 10)->nullable()->after('exchange');
            $table->string('country', 100)->nullable()->after('mic_code');
            $table->string('figi_code', 20)->nullable()->unique()->after('country');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'currency',
                'exchange',
                'mic_code',
                'country',
                'figi_code',
            ]);
        });
    }
};
