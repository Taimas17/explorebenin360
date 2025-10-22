<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('offering_id')->constrained('offerings');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->unsignedInteger('guests')->default(1);
            $table->enum('status', ['pending','authorized','confirmed','cancelled','refunded'])->default('pending');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 8)->default('XOF');
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->string('payment_provider', 50)->default('paystack');
            $table->string('payment_ref', 191)->nullable();
            $table->string('payment_status', 50)->nullable();
            $table->text('cancel_reason')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('offering_id');
            $table->index('status');
            $table->index('payment_ref');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
