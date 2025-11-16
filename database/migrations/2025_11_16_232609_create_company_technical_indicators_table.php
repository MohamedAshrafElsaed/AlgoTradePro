<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_technical_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('interval', 10)->default('1day');

            // Moving Averages
            $table->decimal('sma_20', 15, 5)->nullable();
            $table->decimal('sma_50', 15, 5)->nullable();
            $table->decimal('sma_200', 15, 5)->nullable();
            $table->decimal('ema_12', 15, 5)->nullable();
            $table->decimal('ema_26', 15, 5)->nullable();

            // MACD
            $table->decimal('macd', 15, 5)->nullable();
            $table->decimal('macd_signal', 15, 5)->nullable();
            $table->decimal('macd_hist', 15, 5)->nullable();

            // Bollinger Bands
            $table->decimal('bb_upper', 15, 5)->nullable();
            $table->decimal('bb_middle', 15, 5)->nullable();
            $table->decimal('bb_lower', 15, 5)->nullable();

            // Momentum Indicators
            $table->decimal('rsi_14', 8, 2)->nullable(); // 0-100
            $table->decimal('stoch_k', 8, 2)->nullable(); // 0-100
            $table->decimal('stoch_d', 8, 2)->nullable(); // 0-100
            $table->decimal('cci', 10, 2)->nullable();
            $table->decimal('roc', 10, 2)->nullable();
            $table->decimal('momentum', 15, 5)->nullable();

            // Volume Indicators
            $table->decimal('obv', 20, 2)->nullable();
            $table->decimal('ad', 20, 2)->nullable();
            $table->decimal('adosc', 20, 2)->nullable();

            $table->timestamps();

            // Indexes
            $table->unique(['company_id', 'date', 'interval']);
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_technical_indicators');
    }
};
