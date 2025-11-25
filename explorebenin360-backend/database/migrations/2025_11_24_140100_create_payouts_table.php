<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('restrict');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 8)->default('XOF');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('reference', 191)->unique();
            $table->string('transaction_ref', 191)->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('provider_id');
            $table->index('payment_method_id');
            $table->index('status');
            $table->index('reference');
            $table->index('requested_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
