<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['bank_account', 'mobile_money', 'paypal'])->default('mobile_money');
            $table->string('account_name', 191);
            $table->string('account_number', 191);
            $table->string('bank_name', 191)->nullable();
            $table->string('bank_code', 50)->nullable();
            $table->string('mobile_provider', 50)->nullable();
            $table->string('country', 2)->default('BJ');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->index('type');
            $table->index('is_default');
            $table->index('is_verified');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
