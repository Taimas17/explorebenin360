<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('guides', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('languages_json')->nullable();
            $table->json('specialties_json')->nullable();
            $table->text('bio');
            $table->string('avatar_url')->nullable();
            $table->string('city');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->decimal('price_per_day', 10, 2)->nullable();
            $table->string('currency')->default('XOF');
            $table->boolean('verified')->default(false);
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->enum('status', ['draft','published'])->default('published');
            $table->softDeletes();
            $table->timestamps();
            $table->index('city');
            $table->index('verified');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guides');
    }
};
