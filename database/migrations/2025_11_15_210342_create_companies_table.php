<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Company types table
        Schema::create('company_types', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // Companies table
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_type_id')->constrained()->cascadeOnDelete();
            $table->string('symbol')->unique()->index();
            $table->string('name_en');
            $table->string('name_ar');
            $table->decimal('current_price', 15, 5);
            $table->decimal('price_change', 15, 5)->default(0);
            $table->decimal('change_percentage', 10, 2)->default(0);
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('ceo')->nullable();
            $table->string('headquarter_en')->nullable();
            $table->string('headquarter_ar')->nullable();
            $table->timestamp('last_updated')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('current_price');
            $table->index('company_type_id');
        });

        // Company statistics table
        Schema::create('company_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->decimal('market_cap', 20, 2)->nullable();
            $table->decimal('value_today', 15, 2)->nullable();
            $table->decimal('adtv_6m', 20, 2)->nullable();
            $table->decimal('eps', 10, 2)->nullable();
            $table->decimal('pe_ratio', 10, 2)->nullable();
            $table->decimal('dividend_yield', 5, 2)->nullable();
            $table->decimal('week_52_high', 15, 5)->nullable();
            $table->decimal('week_52_low', 15, 5)->nullable();
            $table->timestamps();

            $table->index('company_id');
        });

        // Company news table
        Schema::create('company_news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('title_en');
            $table->string('title_ar');
            $table->string('source');
            $table->string('url')->nullable();
            $table->timestamp('published_at');
            $table->timestamps();

            $table->index('company_id');
            $table->index('published_at');
        });

        // User favorites table
        Schema::create('user_company_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'company_id']);
            $table->index('user_id');
            $table->index('company_id');
        });

        // Company subscriptions table
        Schema::create('company_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->boolean('notify_recommendations')->default(true);
            $table->boolean('notify_updates')->default(true);
            $table->boolean('notify_news')->default(false);
            $table->boolean('notify_price_alerts')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'company_id']);
            $table->index('user_id');
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_subscriptions');
        Schema::dropIfExists('user_company_favorites');
        Schema::dropIfExists('company_news');
        Schema::dropIfExists('company_statistics');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('company_types');
    }
};
