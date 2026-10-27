<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'referral_id',
        'user_id',
        'activity_type',
        'description',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
