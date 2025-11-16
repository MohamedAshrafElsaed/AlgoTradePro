<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyDividend extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'ex_date',
        'payment_date',
        'record_date',
        'declaration_date',
        'amount',
        'adjusted_amount',
        'currency',
        'dividend_type',
    ];

    protected function casts(): array
    {
        return [
            'ex_date' => 'date',
            'payment_date' => 'date',
            'record_date' => 'date',
            'declaration_date' => 'date',
            'amount' => 'decimal:4',
            'adjusted_amount' => 'decimal:4',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
