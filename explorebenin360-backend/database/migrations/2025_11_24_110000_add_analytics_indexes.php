<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('created_at');
            // last_login_at may be added by a later migration; only create index if column exists
            if (Schema::hasColumn('users', 'last_login_at')) {
                $table->index('last_login_at');
            }
        });
        
        Schema::table('bookings', function (Blueprint $table) {
            $table->index('created_at');
            $table->index(['status', 'created_at']); // Composite pour queries filtrées
        });
        
        Schema::table('offerings', function (Blueprint $table) {
            $table->index('created_at');
        });
        
        Schema::table('accommodations', function (Blueprint $table) {
            $table->index('created_at');
        });
        
        Schema::table('articles', function (Blueprint $table) {
            $table->index('created_at');
        });
        
        Schema::table('events', function (Blueprint $table) {
            $table->index('created_at');
        });
        
        Schema::table('guides', function (Blueprint $table) {
            $table->index('created_at');
        });
        
        Schema::table('places', function (Blueprint $table) {
            $table->index('created_at');
        });
        
        Schema::table('favorites', function (Blueprint $table) {
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'created_at')) {
                $table->dropIndex(['created_at']);
            }
            if (Schema::hasColumn('users', 'last_login_at')) {
                $table->dropIndex(['last_login_at']);
            }
        });
        
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status', 'created_at']);
        });
        
        Schema::table('offerings', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
        
        Schema::table('accommodations', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
        
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
        
        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
        
        Schema::table('guides', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
        
        Schema::table('places', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
        
        Schema::table('favorites', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
    }
};
