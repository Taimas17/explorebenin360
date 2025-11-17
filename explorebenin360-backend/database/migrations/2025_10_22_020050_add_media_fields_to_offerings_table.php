<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('offerings', function (Blueprint $table) {
            $table->string('cover_image_url')->nullable()->after('status');
            $table->json('gallery_json')->nullable()->after('cover_image_url');
            $table->text('cancellation_policy')->nullable()->after('gallery_json');
        });
    }

    public function down(): void
    {
        Schema::table('offerings', function (Blueprint $table) {
            $table->dropColumn(['cover_image_url', 'gallery_json', 'cancellation_policy']);
        });
    }
};
