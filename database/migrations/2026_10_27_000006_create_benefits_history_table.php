<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('benefits_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_id')->constrained('referrals')->cascadeOnDelete();
            $table->foreignId('beneficiary_customer_id')->constrained('customers');
            $table->enum('beneficiary_type', ['referrer', 'referee'])->index();
            $table->decimal('discount_amount', 10, 2)->default(30800.00);
            $table->string('apply_to_period', 7)->nullable(); // e.g. 2026-11
            $table->enum('status', ['granted', 'applied', 'cancelled'])->default('granted')->index();
            $table->foreignId('authorized_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('benefits_history');
    }
};
