<?php

namespace App\Jobs;

use App\Models\Referral;
use App\Services\EvolutionApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyAdminGroupNewReferralJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Referral $referral
    ) {}

    public function handle(EvolutionApiService $evolutionService): void
    {
        $evolutionService->notifyAdminGroup($this->referral);
    }
}
