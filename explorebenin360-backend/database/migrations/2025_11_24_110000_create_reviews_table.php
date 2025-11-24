<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('reviewable_type', 100);
            $table->unsignedBigInteger('reviewable_id');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('booking_id')->nullable()->constrained('bookings');
            $table->unsignedTinyInteger('rating');
            $table->string('title', 255)->nullable();
            $table->text('body');
            $table->text('response')->nullable();
            $table->foreignId('response_by')->nullable()->constrained('users');
            $table->timestamp('response_at')->nullable();
            $table->integer('helpful_count')->default(0);
            $table->enum('status', ['pending','published','rejected'])->default('pending');
            $table->foreignId('moderated_by')->nullable()->constrained('users');
            $table->timestamp('moderated_at')->nullable();
            $table->string('moderation_reason', 500)->nullable();
            $table->boolean('verified_purchase')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['reviewable_type', 'reviewable_id']);
            $table->index('user_id');
            $table->index('booking_id');
            $table->index('status');
            $table->index('rating');
            $table->index('verified_purchase');
            $table->unique(['user_id', 'reviewable_type', 'reviewable_id'], 'uniq_user_reviewable');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
