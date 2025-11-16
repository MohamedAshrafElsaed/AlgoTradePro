<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'market_cap',
        'value_today',
        'adtv_6m',
        'eps',
        'pe_ratio',
        'dividend_yield',
        'week_52_high',
        'week_52_low',
    ];

    protected function casts(): array
    {
        return [
            'market_cap' => 'decimal:2',
            'value_today' => 'decimal:2',
            'adtv_6m' => 'decimal:2',
            'eps' => 'decimal:2',
            'pe_ratio' => 'decimal:2',
            'dividend_yield' => 'decimal:2',
            'week_52_high' => 'decimal:5',
            'week_52_low' => 'decimal:5',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}

// ============================================================================

