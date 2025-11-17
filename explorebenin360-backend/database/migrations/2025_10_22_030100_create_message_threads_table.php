<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('message_threads', function (Blueprint $table) {
            $table->id();
            $table->string('subject')->nullable();
            $table->foreignId('user_id')->constrained('users'); // Initiateur
            $table->foreignId('recipient_id')->constrained('users'); // Destinataire
            $table->foreignId('related_offering_id')->nullable()->constrained('offerings'); // Contexte optionnel
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'recipient_id']);
            $table->index('last_message_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_threads');
    }
};
