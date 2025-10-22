<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt');
            $table->longText('body');
            $table->string('author_name');
            $table->string('category');
            $table->json('tags')->nullable();
            $table->string('cover_image_url')->nullable();
            $table->enum('status', ['draft','published'])->default('published');
            $table->dateTime('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index('category');
            $table->index('status');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
