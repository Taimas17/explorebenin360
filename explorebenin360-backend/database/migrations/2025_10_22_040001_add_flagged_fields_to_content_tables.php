<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->timestamp('flagged_at')->nullable()->after('status');
            $table->string('flagged_reason')->nullable()->after('flagged_at');
        });

        Schema::table('places', function (Blueprint $table) {
            $table->timestamp('flagged_at')->nullable()->after('status');
            $table->string('flagged_reason')->nullable()->after('flagged_at');
        });

        Schema::table('offerings', function (Blueprint $table) {
            $table->timestamp('flagged_at')->nullable()->after('status');
            $table->string('flagged_reason')->nullable()->after('flagged_at');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['flagged_at', 'flagged_reason']);
        });

        Schema::table('places', function (Blueprint $table) {
            $table->dropColumn(['flagged_at', 'flagged_reason']);
        });

        Schema::table('offerings', function (Blueprint $table) {
            $table->dropColumn(['flagged_at', 'flagged_reason']);
        });
    }
};
