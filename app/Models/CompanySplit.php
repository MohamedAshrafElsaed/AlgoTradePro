<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySplit extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'split_date',
        'description',
        'split_ratio',
        'from_factor',
        'to_factor',
    ];

    protected function casts(): array
    {
        return [
            'split_date' => 'date',
            'split_ratio' => 'decimal:4',
            'from_factor' => 'integer',
            'to_factor' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
