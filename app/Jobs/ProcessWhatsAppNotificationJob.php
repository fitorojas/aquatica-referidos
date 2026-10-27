<?php

namespace App\Jobs;

use App\Models\Referral;
use App\Services\EvolutionApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessWhatsAppNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Referral $referral,
        public string $type = 'referee_welcome'
    ) {}

    public function handle(EvolutionApiService $evolutionService): void
    {
        match ($this->type) {
            'referee_welcome' => $evolutionService->sendRefereeWelcome($this->referral),
            'referrer_benefit_approved' => $evolutionService->sendReferrerBenefitApproved($this->referral),
            'referee_benefit_approved' => $evolutionService->sendRefereeBenefitApproved($this->referral),
            default => null,
        };
    }
}
