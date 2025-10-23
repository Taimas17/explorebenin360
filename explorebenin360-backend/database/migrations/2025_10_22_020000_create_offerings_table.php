<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('users');
            $table->foreignId('place_id')->nullable()->constrained('places');
            $table->enum('type', ['accommodation','experience','guide_service']);
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 8)->default('XOF');
            $table->unsignedInteger('capacity')->default(1);
            $table->json('availability_json')->nullable();
            $table->enum('status', ['draft','published'])->default('published');
            $table->softDeletes();
            $table->timestamps();

            $table->index('slug');
            $table->index('provider_id');
            $table->index('type');
            $table->index('status');
            $table->index('price');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offerings');
    }
};
