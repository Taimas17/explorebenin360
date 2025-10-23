<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('place_id')->nullable();
            $table->string('city');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('organizer_name')->nullable();
            $table->string('organizer_contact')->nullable();
            $table->text('description');
            $table->decimal('price', 10, 2)->nullable();
            $table->string('currency')->default('XOF');
            $table->string('category');
            $table->string('cover_image_url')->nullable();
            $table->enum('status', ['draft','published'])->default('published');
            $table->boolean('featured')->default(false);
            $table->softDeletes();
            $table->timestamps();
            $table->index('city');
            $table->index('category');
            $table->index('status');
            $table->index('start_date');
            $table->index('end_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
