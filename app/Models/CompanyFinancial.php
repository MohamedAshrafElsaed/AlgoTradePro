<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyFinancial extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'fiscal_date',
        'period',
        'statement_type',
        'revenue',
        'cost_of_revenue',
        'gross_profit',
        'operating_expense',
        'operating_income',
        'ebitda',
        'ebit',
        'interest_expense',
        'income_before_tax',
        'income_tax_expense',
        'net_income',
        'eps',
        'eps_diluted',
        'weighted_average_shares',
        'weighted_average_shares_diluted',
        'total_assets',
        'current_assets',
        'cash_and_equivalents',
        'cash_and_short_term_investments',
        'accounts_receivable',
        'inventory',
        'non_current_assets',
        'property_plant_equipment',
        'intangible_assets',
        'goodwill',
        'total_liabilities',
        'current_liabilities',
        'accounts_payable',
        'short_term_debt',
        'non_current_liabilities',
        'long_term_debt',
        'shareholders_equity',
        'retained_earnings',
        'operating_cash_flow',
        'capital_expenditure',
        'free_cash_flow',
        'investing_cash_flow',
        'financing_cash_flow',
        'dividend_payments',
        'stock_repurchase',
        'net_change_in_cash',
    ];

    protected function casts(): array
    {
        return [
            'fiscal_date' => 'date',
            'revenue' => 'decimal:2',
            'cost_of_revenue' => 'decimal:2',
            'gross_profit' => 'decimal:2',
            'operating_expense' => 'decimal:2',
            'operating_income' => 'decimal:2',
            'ebitda' => 'decimal:2',
            'ebit' => 'decimal:2',
            'interest_expense' => 'decimal:2',
            'income_before_tax' => 'decimal:2',
            'income_tax_expense' => 'decimal:2',
            'net_income' => 'decimal:2',
            'eps' => 'decimal:4',
            'eps_diluted' => 'decimal:4',
            'total_assets' => 'decimal:2',
            'current_assets' => 'decimal:2',
            'cash_and_equivalents' => 'decimal:2',
            'cash_and_short_term_investments' => 'decimal:2',
            'accounts_receivable' => 'decimal:2',
            'inventory' => 'decimal:2',
            'non_current_assets' => 'decimal:2',
            'property_plant_equipment' => 'decimal:2',
            'intangible_assets' => 'decimal:2',
            'goodwill' => 'decimal:2',
            'total_liabilities' => 'decimal:2',
            'current_liabilities' => 'decimal:2',
            'accounts_payable' => 'decimal:2',
            'short_term_debt' => 'decimal:2',
            'non_current_liabilities' => 'decimal:2',
            'long_term_debt' => 'decimal:2',
            'shareholders_equity' => 'decimal:2',
            'retained_earnings' => 'decimal:2',
            'operating_cash_flow' => 'decimal:2',
            'capital_expenditure' => 'decimal:2',
            'free_cash_flow' => 'decimal:2',
            'investing_cash_flow' => 'decimal:2',
            'financing_cash_flow' => 'decimal:2',
            'dividend_payments' => 'decimal:2',
            'stock_repurchase' => 'decimal:2',
            'net_change_in_cash' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
