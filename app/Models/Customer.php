<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'identification',
        'phone',
        'email',
        'customer_type',
        'notes',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function referralsGiven(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_customer_id');
    }

    public function referralsReceived(): HasMany
    {
        return $this->hasMany(Referral::class, 'referee_customer_id');
    }

    public function benefits(): HasMany
    {
        return $this->hasMany(BenefitHistory::class, 'beneficiary_customer_id');
    }

    /**
     * Limpia y normaliza el teléfono para WhatsApp / E.164
     */
    public function getNormalizedPhoneAttribute(): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $this->phone ?? '');
        if (strlen($cleaned) === 8) {
            return '506' . $cleaned;
        }
        return $cleaned;
    }
}
