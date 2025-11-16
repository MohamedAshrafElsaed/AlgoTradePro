<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_splits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->date('split_date');
            $table->string('description')->nullable(); // e.g., "2-for-1 split"
            $table->decimal('split_ratio', 10, 4)->nullable(); // e.g., 2.0 for 2-for-1
            $table->integer('from_factor')->nullable(); // e.g., 2 in "2-for-1"
            $table->integer('to_factor')->nullable(); // e.g., 1 in "2-for-1"

            $table->timestamps();

            // Indexes
            $table->unique(['company_id', 'split_date']);
            $table->index('split_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_splits');
    }
};
