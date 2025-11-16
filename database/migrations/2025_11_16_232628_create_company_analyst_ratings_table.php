<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_analyst_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->date('rating_date');

            $table->string('analyst_name')->nullable();
            $table->string('analyst_firm')->nullable();
            $table->string('rating', 50)->nullable(); // Overweight, Underweight, Buy, Sell, etc.
            $table->string('previous_rating', 50)->nullable();
            $table->enum('action', ['Maintains', 'Upgrade', 'Downgrade', 'Initiates', 'Reiterates'])->nullable();
            $table->decimal('price_target', 15, 5)->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['company_id', 'rating_date']);
            $table->index('rating_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_analyst_ratings');
    }
};
