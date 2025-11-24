<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('account_status', ['active','suspended','banned'])->default('active')->after('provider_status');
            $table->timestamp('suspended_at')->nullable()->after('account_status');
            $table->foreignId('suspended_by')->nullable()->constrained('users')->nullOnDelete()->after('suspended_at');
            $table->text('suspension_reason')->nullable()->after('suspended_by');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            $table->unsignedInteger('login_count')->default(0)->after('last_login_ip');
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }

            $table->index('account_status');
            $table->index('suspended_by');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'account_status')) $table->dropColumn('account_status');
            if (Schema::hasColumn('users', 'suspended_at')) $table->dropColumn('suspended_at');
            if (Schema::hasColumn('users', 'suspended_by')) $table->dropConstrainedForeignId('suspended_by');
            if (Schema::hasColumn('users', 'suspension_reason')) $table->dropColumn('suspension_reason');
            if (Schema::hasColumn('users', 'last_login_at')) $table->dropColumn('last_login_at');
            if (Schema::hasColumn('users', 'last_login_ip')) $table->dropColumn('last_login_ip');
            if (Schema::hasColumn('users', 'login_count')) $table->dropColumn('login_count');
            if (Schema::hasColumn('users', 'deleted_at')) $table->dropSoftDeletes();
        });
    }
};