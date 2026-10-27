<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->string('referral_code', 30)->unique();
            $table->foreignId('referrer_customer_id')->constrained('customers');
            $table->foreignId('referee_customer_id')->constrained('customers');
            $table->foreignId('referee_student_id')->nullable()->constrained('students');
            $table->foreignId('service_id')->constrained('services');
            $table->enum('status', [
                'pending',
                'in_contact',
                'eligible',
                'enrolled',
                'paid',
                'benefit_approved',
                'rejected',
                'expired'
            ])->default('pending')->index();
            $table->text('admin_notes')->nullable();
            $table->timestamp('contacted_at')->nullable();
            $table->timestamp('eligible_at')->nullable();
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('benefit_approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
