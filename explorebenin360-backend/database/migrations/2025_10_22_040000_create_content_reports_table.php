<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('content_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users');
            $table->string('reportable_type');
            $table->unsignedBigInteger('reportable_id');
            $table->enum('reason', ['spam', 'inappropriate', 'offensive', 'fake', 'other']);
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'reviewing', 'resolved', 'dismissed'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->text('resolution_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['reportable_type', 'reportable_id']);
            $table->index('status');
            $table->index('reporter_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_reports');
    }
};
