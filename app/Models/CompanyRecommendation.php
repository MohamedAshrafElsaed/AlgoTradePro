<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'strong_buy_count',
        'buy_count',
        'hold_count',
        'sell_count',
        'strong_sell_count',
        'recommendation_mean',
        'recommendation_key',
        'price_target_average',
        'price_target_high',
        'price_target_low',
        'price_target_median',
        'number_of_analysts',
        'ai_recommendation',
        'ai_confidence',
        'ai_reasoning',
        'last_updated',
    ];

    protected function casts(): array
    {
        return [
            'strong_buy_count' => 'integer',
            'buy_count' => 'integer',
            'hold_count' => 'integer',
            'sell_count' => 'integer',
            'strong_sell_count' => 'integer',
            'recommendation_mean' => 'decimal:2',
            'price_target_average' => 'decimal:5',
            'price_target_high' => 'decimal:5',
            'price_target_low' => 'decimal:5',
            'price_target_median' => 'decimal:5',
            'number_of_analysts' => 'integer',
            'ai_confidence' => 'decimal:2',
            'last_updated' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
