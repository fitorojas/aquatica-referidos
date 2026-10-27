<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('duplicate_detections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_id')->constrained('referrals')->cascadeOnDelete();
            $table->string('matched_field', 50)->index(); // phone, identification, email, student_name
            $table->string('matched_value', 255);
            $table->unsignedBigInteger('existing_reference_id')->nullable();
            $table->decimal('match_confidence', 5, 2)->default(100.00);
            $table->boolean('is_resolved')->default(false)->index();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duplicate_detections');
    }
};
