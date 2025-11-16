<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyTechnicalIndicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'date',
        'interval',
        'sma_20',
        'sma_50',
        'sma_200',
        'ema_12',
        'ema_26',
        'macd',
        'macd_signal',
        'macd_hist',
        'bb_upper',
        'bb_middle',
        'bb_lower',
        'rsi_14',
        'stoch_k',
        'stoch_d',
        'cci',
        'roc',
        'momentum',
        'obv',
        'ad',
        'adosc',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'sma_20' => 'decimal:5',
            'sma_50' => 'decimal:5',
            'sma_200' => 'decimal:5',
            'ema_12' => 'decimal:5',
            'ema_26' => 'decimal:5',
            'macd' => 'decimal:5',
            'macd_signal' => 'decimal:5',
            'macd_hist' => 'decimal:5',
            'bb_upper' => 'decimal:5',
            'bb_middle' => 'decimal:5',
            'bb_lower' => 'decimal:5',
            'rsi_14' => 'decimal:2',
            'stoch_k' => 'decimal:2',
            'stoch_d' => 'decimal:2',
            'cci' => 'decimal:2',
            'roc' => 'decimal:2',
            'momentum' => 'decimal:5',
            'obv' => 'decimal:2',
            'ad' => 'decimal:2',
            'adosc' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
