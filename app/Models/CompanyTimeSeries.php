<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyTimeSeries extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'date',
        'interval',
        'open',
        'high',
        'low',
        'close',
        'volume',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'open' => 'decimal:5',
            'high' => 'decimal:5',
            'low' => 'decimal:5',
            'close' => 'decimal:5',
            'volume' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
