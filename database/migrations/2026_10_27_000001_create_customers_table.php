<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 180);
            $table->string('identification', 30)->nullable()->index();
            $table->string('phone', 25)->index();
            $table->string('email', 120)->nullable()->index();
            $table->enum('customer_type', ['guardian', 'adult_student', 'both'])->default('both');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
