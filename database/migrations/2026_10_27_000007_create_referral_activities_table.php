<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_id')->constrained('referrals')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('activity_type', 50)->index(); // status_change, call, whatsapp_sent, note
            $table->string('description', 255);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_activities');
    }
};
