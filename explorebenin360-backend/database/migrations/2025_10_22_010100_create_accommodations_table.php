<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('accommodations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->unsignedBigInteger('place_id')->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('type', ['hotel','guesthouse','ecolodge','bnb','other']);
            $table->longText('description');
            $table->string('address');
            $table->string('city');
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->decimal('price_per_night', 10, 2);
            $table->string('currency')->default('XOF');
            $table->json('amenities_json')->nullable();
            $table->unsignedInteger('capacity')->default(2);
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->boolean('featured')->default(false);
            $table->enum('status', ['draft','published'])->default('published');
            $table->string('cover_image_url')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index('type');
            $table->index('city');
            $table->index('price_per_night');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accommodations');
    }
};
