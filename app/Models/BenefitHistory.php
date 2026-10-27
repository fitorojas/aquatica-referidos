<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BenefitHistory extends Model
{
    use HasFactory;

    protected $table = 'benefits_history';

    protected $fillable = [
        'referral_id',
        'beneficiary_customer_id',
        'beneficiary_type',
        'discount_amount',
        'apply_to_period',
        'status',
        'authorized_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'discount_amount' => 'decimal:2',
        ];
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class);
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'beneficiary_customer_id');
    }

    public function authorizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }

    public function getBeneficiaryTypeLabelAttribute(): string
    {
        return match ($this->beneficiary_type) {
            'referrer' => 'Cliente Referente',
            'referee' => 'Cliente Referido',
            default => ucfirst($this->beneficiary_type),
        };
    }
}
