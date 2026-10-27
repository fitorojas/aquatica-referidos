<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'referral_id',
        'user_id',
        'verification_type',
        'result',
        'receipt_number',
        'payment_amount',
        'proof_document_path',
        'observations',
    ];

    protected function casts(): array
    {
        return [
            'payment_amount' => 'decimal:2',
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

    public function getTypeLabelAttribute(): string
    {
        return match ($this->verification_type) {
            'eligibility' => 'Elegibilidad de Alumno',
            'enrollment' => 'Matrícula en Servicio',
            'payment' => 'Comprobante de Pago',
            default => ucfirst($this->verification_type),
        };
    }
}
