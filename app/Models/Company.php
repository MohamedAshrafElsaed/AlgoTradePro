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
        'currency',
        'exchange',
        'mic_code',
        'country',
        'figi_code',
        'name_en',
        'name_ar',
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
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function companyType(): BelongsTo
    {
        return $this->belongsTo(CompanyType::class);
    }

    public function statistics(): HasOne
    {
        return $this->hasOne(CompanyStatistic::class);
    }

    public function news(): HasMany
    {
        return $this->hasMany(CompanyNews::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_company_favorites')
            ->withTimestamps();
    }

    public function subscribedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_subscriptions')
            ->withPivot(['notify_recommendations', 'notify_updates', 'notify_news', 'notify_price_alerts'])
            ->withTimestamps();
    }

    public function isFavoritedBy(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }

        return $this->favoritedBy()->where('user_id', $userId)->exists();
    }

    public function isSubscribedBy(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }

        return $this->subscribedBy()->where('user_id', $userId)->exists();
    }
}
