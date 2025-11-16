<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_dividends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->date('ex_date');
            $table->date('payment_date')->nullable();
            $table->date('record_date')->nullable();
            $table->date('declaration_date')->nullable();

            $table->decimal('amount', 10, 4);
            $table->decimal('adjusted_amount', 10, 4)->nullable();
            $table->string('currency', 10)->nullable();
            $table->string('dividend_type', 50)->nullable(); // Regular, Special, etc.

            $table->timestamps();

            // Indexes
            $table->unique(['company_id', 'ex_date']);
            $table->index('ex_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_dividends');
    }
};
