<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    // NEW: Company relationships
    public function favoriteCompanies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'user_company_favorites')
            ->withTimestamps();
    }

    public function subscribedCompanies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_subscriptions')
            ->withPivot(['notify_recommendations', 'notify_updates', 'notify_news', 'notify_price_alerts'])
            ->withTimestamps();
    }
}
