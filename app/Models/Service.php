<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'regular_price',
        'promo_price',
        'is_active',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'regular_price' => 'decimal:2',
            'promo_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }
}
