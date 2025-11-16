<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyEarning extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'earnings_date',
        'time',
        'eps_estimate',
        'eps_actual',
        'revenue_estimate',
        'revenue_actual',
        'fiscal_date_ending',
        'period',
    ];

    protected function casts(): array
    {
        return [
            'earnings_date' => 'date',
            'eps_estimate' => 'decimal:4',
            'eps_actual' => 'decimal:4',
            'revenue_estimate' => 'decimal:2',
            'revenue_actual' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
