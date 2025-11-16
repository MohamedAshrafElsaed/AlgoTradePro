<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            // Analyst Consensus
            $table->integer('strong_buy_count')->default(0);
            $table->integer('buy_count')->default(0);
            $table->integer('hold_count')->default(0);
            $table->integer('sell_count')->default(0);
            $table->integer('strong_sell_count')->default(0);
            $table->decimal('recommendation_mean', 3, 2)->nullable(); // 1.0 to 5.0
            $table->string('recommendation_key', 50)->nullable(); // Strong Buy, Buy, Hold, Sell

            // Price Targets
            $table->decimal('price_target_average', 15, 5)->nullable();
            $table->decimal('price_target_high', 15, 5)->nullable();
            $table->decimal('price_target_low', 15, 5)->nullable();
            $table->decimal('price_target_median', 15, 5)->nullable();
            $table->integer('number_of_analysts')->default(0);

            // AI Recommendation (Your own system)
            $table->enum('ai_recommendation', ['STRONG_BUY', 'BUY', 'HOLD', 'SELL', 'STRONG_SELL'])->nullable();
            $table->decimal('ai_confidence', 5, 2)->nullable(); // 0-100
            $table->text('ai_reasoning')->nullable();

            $table->timestamp('last_updated')->nullable();
            $table->timestamps();

            // Only one recommendation per company (latest)
            $table->unique('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_recommendations');
    }
};
