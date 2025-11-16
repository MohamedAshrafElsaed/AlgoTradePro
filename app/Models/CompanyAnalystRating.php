<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyAnalystRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'rating_date',
        'analyst_name',
        'analyst_firm',
        'rating',
        'previous_rating',
        'action',
        'price_target',
    ];

    protected function casts(): array
    {
        return [
            'rating_date' => 'date',
            'price_target' => 'decimal:5',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
