<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_financials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->date('fiscal_date');
            $table->enum('period', ['annual', 'quarterly'])->default('annual');
            $table->enum('statement_type', ['income', 'balance', 'cash_flow']); // Type of statement

            // Income Statement Fields
            $table->decimal('revenue', 20, 2)->nullable();
            $table->decimal('cost_of_revenue', 20, 2)->nullable();
            $table->decimal('gross_profit', 20, 2)->nullable();
            $table->decimal('operating_expense', 20, 2)->nullable();
            $table->decimal('operating_income', 20, 2)->nullable();
            $table->decimal('ebitda', 20, 2)->nullable();
            $table->decimal('ebit', 20, 2)->nullable();
            $table->decimal('interest_expense', 20, 2)->nullable();
            $table->decimal('income_before_tax', 20, 2)->nullable();
            $table->decimal('income_tax_expense', 20, 2)->nullable();
            $table->decimal('net_income', 20, 2)->nullable();
            $table->decimal('eps', 10, 4)->nullable();
            $table->decimal('eps_diluted', 10, 4)->nullable();
            $table->bigInteger('weighted_average_shares', false)->nullable();
            $table->bigInteger('weighted_average_shares_diluted', false)->nullable();

            // Balance Sheet Fields
            $table->decimal('total_assets', 20, 2)->nullable();
            $table->decimal('current_assets', 20, 2)->nullable();
            $table->decimal('cash_and_equivalents', 20, 2)->nullable();
            $table->decimal('cash_and_short_term_investments', 20, 2)->nullable();
            $table->decimal('accounts_receivable', 20, 2)->nullable();
            $table->decimal('inventory', 20, 2)->nullable();
            $table->decimal('non_current_assets', 20, 2)->nullable();
            $table->decimal('property_plant_equipment', 20, 2)->nullable();
            $table->decimal('intangible_assets', 20, 2)->nullable();
            $table->decimal('goodwill', 20, 2)->nullable();

            $table->decimal('total_liabilities', 20, 2)->nullable();
            $table->decimal('current_liabilities', 20, 2)->nullable();
            $table->decimal('accounts_payable', 20, 2)->nullable();
            $table->decimal('short_term_debt', 20, 2)->nullable();
            $table->decimal('non_current_liabilities', 20, 2)->nullable();
            $table->decimal('long_term_debt', 20, 2)->nullable();

            $table->decimal('shareholders_equity', 20, 2)->nullable();
            $table->decimal('retained_earnings', 20, 2)->nullable();

            // Cash Flow Fields
            $table->decimal('operating_cash_flow', 20, 2)->nullable();
            $table->decimal('capital_expenditure', 20, 2)->nullable();
            $table->decimal('free_cash_flow', 20, 2)->nullable();
            $table->decimal('investing_cash_flow', 20, 2)->nullable();
            $table->decimal('financing_cash_flow', 20, 2)->nullable();
            $table->decimal('dividend_payments', 20, 2)->nullable();
            $table->decimal('stock_repurchase', 20, 2)->nullable();
            $table->decimal('net_change_in_cash', 20, 2)->nullable();

            $table->timestamps();

            // Indexes
            $table->unique(['company_id', 'fiscal_date', 'period', 'statement_type'], 'unique_financial_record');
            $table->index('fiscal_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_financials');
    }
};
