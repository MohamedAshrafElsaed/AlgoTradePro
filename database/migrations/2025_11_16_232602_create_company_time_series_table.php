<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_time_series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('interval', 10)->default('1day'); // 1min, 5min, 1day, 1week, etc.

            // OHLCV Data
            $table->decimal('open', 15, 5)->nullable();
            $table->decimal('high', 15, 5)->nullable();
            $table->decimal('low', 15, 5)->nullable();
            $table->decimal('close', 15, 5)->nullable();
            $table->bigInteger('volume')->nullable();

            $table->timestamps();

            // Indexes
            $table->unique(['company_id', 'date', 'interval']);
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_time_series');
    }
};
