<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_url', 500)->nullable()->after('remember_token');
            $table->string('cover_image_url', 500)->nullable()->after('avatar_url');
            $table->date('date_of_birth')->nullable()->after('cover_image_url');
            $table->enum('gender', ['male','female','other','prefer_not_to_say'])->nullable()->after('date_of_birth');
            $table->string('country', 100)->nullable()->after('gender');
            $table->string('city', 100)->nullable()->after('country');
            $table->string('address', 500)->nullable()->after('city');
            $table->string('postal_code', 20)->nullable()->after('address');
            $table->string('website_url', 500)->nullable()->after('postal_code');
            $table->json('social_links')->nullable()->after('website_url');
            $table->json('preferences')->nullable()->after('social_links');
            $table->text('about_me')->nullable()->after('preferences');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar_url',
                'cover_image_url',
                'date_of_birth',
                'gender',
                'country',
                'city',
                'address',
                'postal_code',
                'website_url',
                'social_links',
                'preferences',
                'about_me',
            ]);
        });
    }
};
