<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DuplicateDetection extends Model
{
    use HasFactory;

    protected $fillable = [
        'referral_id',
        'matched_field',
        'matched_value',
        'existing_reference_id',
        'match_confidence',
        'is_resolved',
        'resolution_notes',
    ];

    protected function casts(): array
    {
        return [
            'match_confidence' => 'decimal:2',
            'is_resolved' => 'boolean',
        ];
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class);
    }

    public function existingReference(): BelongsTo
    {
        return $this->belongsTo(Referral::class, 'existing_reference_id');
    }
}
