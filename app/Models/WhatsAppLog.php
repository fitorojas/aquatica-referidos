<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppLog extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_logs';

    protected $fillable = [
        'referral_id',
        'phone',
        'message_type',
        'direction',
        'message_body',
        'payload',
        'status',
        'evolution_message_id',
        'error_message',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class);
    }
}
