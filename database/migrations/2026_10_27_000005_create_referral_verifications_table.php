<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_id')->constrained('referrals')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->enum('verification_type', ['eligibility', 'enrollment', 'payment'])->index();
            $table->enum('result', ['approved', 'rejected', 'pending_info'])->default('approved');
            $table->string('receipt_number', 100)->nullable();
            $table->decimal('payment_amount', 10, 2)->nullable();
            $table->string('proof_document_path', 255)->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_verifications');
    }
};
