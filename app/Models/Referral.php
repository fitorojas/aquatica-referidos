<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'referral_code',
        'referrer_customer_id',
        'referee_customer_id',
        'referee_student_id',
        'service_id',
        'status',
        'admin_notes',
        'contacted_at',
        'eligible_at',
        'enrolled_at',
        'paid_at',
        'benefit_approved_at',
        'rejected_at',
    ];

    protected function casts(): array
    {
        return [
            'contacted_at' => 'datetime',
            'eligible_at' => 'datetime',
            'enrolled_at' => 'datetime',
            'paid_at' => 'datetime',
            'benefit_approved_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'referrer_customer_id');
    }

    public function referee(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'referee_customer_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'referee_student_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(ReferralVerification::class)->latest();
    }

    public function benefits(): HasMany
    {
        return $this->hasMany(BenefitHistory::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ReferralActivity::class)->latest();
    }

    public function duplicateDetections(): HasMany
    {
        return $this->hasMany(DuplicateDetection::class);
    }

    public function whatsappLogs(): HasMany
    {
        return $this->hasMany(WhatsAppLog::class)->latest();
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Nuevo Registrado',
            'in_contact' => 'En Contacto',
            'eligible' => 'Elegibilidad Aprobada',
            'enrolled' => 'Matriculado',
            'paid' => 'Pago Verificado',
            'benefit_approved' => 'Beneficio Otorgado (₡30.800)',
            'rejected' => 'Rechazado / No Elegible',
            'expired' => 'Vencido',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-100 text-amber-800 border-amber-300',
            'in_contact' => 'bg-blue-100 text-blue-800 border-blue-300',
            'eligible' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
            'enrolled' => 'bg-cyan-100 text-cyan-800 border-cyan-300',
            'paid' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'benefit_approved' => 'bg-green-100 text-green-900 border-green-400 font-bold',
            'rejected' => 'bg-rose-100 text-rose-800 border-rose-300',
            'expired' => 'bg-slate-100 text-slate-800 border-slate-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }
}
