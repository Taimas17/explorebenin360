<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('type', ['city','site','museum','park','beach','culture','history','gastronomy','adventure','other']);
            $table->longText('description');
            $table->string('city');
            $table->string('country')->default('Benin');
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->decimal('price_from', 10, 2)->nullable();
            $table->json('opening_hours_json')->nullable();
            $table->json('tags')->nullable();
            $table->string('cover_image_url')->nullable();
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->boolean('featured')->default(false);
            $table->enum('status', ['draft','published'])->default('published');
            $table->softDeletes();
            $table->timestamps();
            $table->index('type');
            $table->index('city');
            $table->index('featured');
            $table->index('status');
            $table->index(['lat','lng']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('places');
    }
};
