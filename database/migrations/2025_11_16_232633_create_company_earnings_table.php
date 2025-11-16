<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->date('earnings_date');
            $table->enum('time', ['Before Hours', 'After Hours', 'Time Not Supplied'])->nullable();

            $table->decimal('eps_estimate', 10, 4)->nullable();
            $table->decimal('eps_actual', 10, 4)->nullable();
            $table->decimal('revenue_estimate', 20, 2)->nullable();
            $table->decimal('revenue_actual', 20, 2)->nullable();

            $table->string('fiscal_date_ending', 20)->nullable();
            $table->enum('period', ['Q1', 'Q2', 'Q3', 'Q4', 'Annual'])->nullable();

            $table->timestamps();

            // Indexes
            $table->unique(['company_id', 'earnings_date']);
            $table->index('earnings_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_earnings');
    }
};
