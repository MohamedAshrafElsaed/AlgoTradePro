<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_statistics', function (Blueprint $table) {
            // Valuation Metrics
            $table->decimal('enterprise_value', 20, 2)->nullable()->after('market_cap');
            $table->decimal('price_to_sales_ratio', 10, 4)->nullable()->after('pe_ratio');
            $table->decimal('price_to_book_ratio', 10, 4)->nullable()->after('price_to_sales_ratio');
            $table->decimal('forward_pe', 10, 4)->nullable()->after('pe_ratio');
            $table->decimal('peg_ratio', 10, 4)->nullable()->after('forward_pe');
            $table->decimal('ev_to_revenue', 10, 4)->nullable()->after('enterprise_value');
            $table->decimal('ev_to_ebitda', 10, 4)->nullable()->after('ev_to_revenue');

            // Profitability Metrics
            $table->decimal('profit_margin', 8, 4)->nullable()->after('dividend_yield');
            $table->decimal('operating_margin', 8, 4)->nullable()->after('profit_margin');
            $table->decimal('return_on_assets', 8, 4)->nullable()->after('operating_margin');
            $table->decimal('return_on_equity', 8, 4)->nullable()->after('return_on_assets');
            $table->decimal('revenue', 20, 2)->nullable()->after('return_on_equity');
            $table->decimal('revenue_per_share', 10, 4)->nullable()->after('revenue');
            $table->decimal('quarterly_revenue_growth', 8, 4)->nullable()->after('revenue_per_share');
            $table->decimal('gross_profit', 20, 2)->nullable()->after('quarterly_revenue_growth');
            $table->decimal('ebitda', 20, 2)->nullable()->after('gross_profit');
            $table->decimal('net_income_to_common', 20, 2)->nullable()->after('ebitda');
            $table->decimal('trailing_eps', 10, 4)->nullable()->after('eps');
            $table->decimal('forward_eps', 10, 4)->nullable()->after('trailing_eps');
            $table->decimal('quarterly_earnings_growth', 8, 4)->nullable()->after('forward_eps');

            // Price Performance
            $table->decimal('beta', 8, 4)->nullable()->after('week_52_low');
            $table->decimal('52_week_change', 8, 4)->nullable()->after('beta');
            $table->decimal('sp500_52_week_change', 8, 4)->nullable()->after('52_week_change');

            // Share Statistics
            $table->bigInteger('shares_outstanding')->nullable()->after('sp500_52_week_change');
            $table->bigInteger('shares_float')->nullable()->after('shares_outstanding');
            $table->decimal('percent_held_by_insiders', 8, 4)->nullable()->after('shares_float');
            $table->decimal('percent_held_by_institutions', 8, 4)->nullable()->after('percent_held_by_insiders');
            $table->bigInteger('shares_short')->nullable()->after('percent_held_by_institutions');
            $table->decimal('short_ratio', 8, 4)->nullable()->after('shares_short');
            $table->decimal('short_percent_of_float', 8, 4)->nullable()->after('short_ratio');

            // Dividend Information
            $table->decimal('payout_ratio', 8, 4)->nullable()->after('dividend_yield');
            $table->date('dividend_date')->nullable()->after('payout_ratio');
            $table->date('ex_dividend_date')->nullable()->after('dividend_date');
            $table->date('last_split_date')->nullable()->after('ex_dividend_date');
            $table->string('last_split_factor', 20)->nullable()->after('last_split_date');

            // Financial Health
            $table->decimal('total_cash', 20, 2)->nullable()->after('last_split_factor');
            $table->decimal('total_cash_per_share', 10, 4)->nullable()->after('total_cash');
            $table->decimal('total_debt', 20, 2)->nullable()->after('total_cash_per_share');
            $table->decimal('debt_to_equity', 10, 4)->nullable()->after('total_debt');
            $table->decimal('current_ratio', 10, 4)->nullable()->after('debt_to_equity');
            $table->decimal('book_value_per_share', 10, 4)->nullable()->after('current_ratio');
            $table->decimal('operating_cash_flow', 20, 2)->nullable()->after('book_value_per_share');
            $table->decimal('levered_free_cash_flow', 20, 2)->nullable()->after('operating_cash_flow');
        });
    }

    public function down(): void
    {
        Schema::table('company_statistics', function (Blueprint $table) {
            $table->dropColumn([
                'enterprise_value', 'price_to_sales_ratio', 'price_to_book_ratio',
                'forward_pe', 'peg_ratio', 'ev_to_revenue', 'ev_to_ebitda',
                'profit_margin', 'operating_margin', 'return_on_assets', 'return_on_equity',
                'revenue', 'revenue_per_share', 'quarterly_revenue_growth',
                'gross_profit', 'ebitda', 'net_income_to_common',
                'trailing_eps', 'forward_eps', 'quarterly_earnings_growth',
                'beta', '52_week_change', 'sp500_52_week_change',
                'shares_outstanding', 'shares_float', 'percent_held_by_insiders',
                'percent_held_by_institutions', 'shares_short', 'short_ratio', 'short_percent_of_float',
                'payout_ratio', 'dividend_date', 'ex_dividend_date',
                'last_split_date', 'last_split_factor',
                'total_cash', 'total_cash_per_share', 'total_debt', 'debt_to_equity',
                'current_ratio', 'book_value_per_share', 'operating_cash_flow', 'levered_free_cash_flow',
            ]);
        });
    }
};
