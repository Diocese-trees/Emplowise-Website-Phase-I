<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class AuditRequest extends Model
{
    protected $fillable = [
        'reference',
        'contact_name',
        'company_name',
        'email',
        'phone',
        'headcount_band',
        'pain_points',
        'notes',
        'preferred_date',
        'preferred_slot',
        'status',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'pain_points' => 'array',
            'preferred_date' => 'date',
        ];
    }
}
