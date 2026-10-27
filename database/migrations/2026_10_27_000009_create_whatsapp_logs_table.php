<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_id')->nullable()->constrained('referrals')->nullOnDelete();
            $table->string('phone', 30)->index();
            $table->string('message_type', 50)->index(); // referee_welcome, referrer_update, admin_group_alert
            $table->enum('direction', ['outgoing', 'incoming'])->default('outgoing');
            $table->text('message_body')->nullable();
            $table->json('payload')->nullable();
            $table->enum('status', ['pending', 'sent', 'delivered', 'read', 'failed'])->default('pending')->index();
            $table->string('evolution_message_id', 150)->nullable()->index();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');
    }
};
