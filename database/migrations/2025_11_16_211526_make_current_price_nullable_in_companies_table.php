<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->decimal('current_price', 15, 5)->nullable()->change();
            $table->decimal('price_change', 15, 5)->nullable()->default(0)->change();
            $table->decimal('change_percentage', 8, 2)->nullable()->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->decimal('current_price', 15, 5)->nullable(false)->change();
            $table->decimal('price_change', 15, 5)->nullable(false)->change();
            $table->decimal('change_percentage', 8, 2)->nullable(false)->change();
        });
    }
};
