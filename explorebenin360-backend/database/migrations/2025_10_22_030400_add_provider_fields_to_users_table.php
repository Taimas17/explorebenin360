<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('business_name')->nullable()->after('name');
            $table->text('bio')->nullable();
            $table->enum('provider_status', ['none', 'pending', 'approved', 'rejected', 'suspended'])->default('none');
            $table->boolean('kyc_submitted')->default(false);
            $table->boolean('kyc_verified')->default(false);
            $table->json('kyc_documents')->nullable();
            $table->text('provider_rejection_reason')->nullable();
            $table->timestamp('provider_approved_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'business_name', 'bio', 'provider_status', 
                'kyc_submitted', 'kyc_verified', 'kyc_documents', 
                'provider_rejection_reason', 'provider_approved_at'
            ]);
        });
    }
};
