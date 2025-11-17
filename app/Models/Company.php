<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_type_id',
        'symbol',
        'name_en',
        'name_ar',
        'currency',
        'exchange',
        'mic_code',
        'country',
        'figi_code',
        'current_price',
        'price_change',
        'change_percentage',
        'description_en',
        'description_ar',
        'ceo',
        'headquarter_en',
        'headquarter_ar',
        'last_updated',
    ];

    protected function casts(): array
    {
        return [
            'current_price' => 'decimal:5',
            'price_change' => 'decimal:5',
            'change_percentage' => 'decimal:2',
            'last_updated' => 'datetime',
        ];
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(CompanyType::class, 'company_type_id');
    }

    public function statistics(): HasOne
    {
        return $this->hasOne(CompanyStatistic::class);
    }

    public function news(): HasMany
    {
        return $this->hasMany(CompanyNews::class);
    }

    public function timeSeries(): HasMany
    {
        return $this->hasMany(CompanyTimeSeries::class);
    }

    public function technicalIndicators(): HasMany
    {
        return $this->hasMany(CompanyTechnicalIndicator::class);
    }

    public function financials(): HasMany
    {
        return $this->hasMany(CompanyFinancial::class);
    }

    public function recommendation(): HasOne
    {
        return $this->hasOne(CompanyRecommendation::class);
    }

    public function analystRatings(): HasMany
    {
        return $this->hasMany(CompanyAnalystRating::class);
    }

    public function earnings(): HasMany
    {
        return $this->hasMany(CompanyEarning::class);
    }

    public function dividends(): HasMany
    {
        return $this->hasMany(CompanyDividend::class);
    }

    public function splits(): HasMany
    {
        return $this->hasMany(CompanySplit::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_company_favorites')
            ->withTimestamps();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(CompanySubscription::class);
    }
}
