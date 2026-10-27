<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'full_name',
        'birth_date',
        'gender',
        'is_same_as_customer',
        'medical_notes',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'is_same_as_customer' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class, 'referee_student_id');
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date ? Carbon::parse($this->birth_date)->age : null;
    }
}
