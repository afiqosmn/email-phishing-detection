<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gmail_syncs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('sync_type', ['initial', 'incremental'])->default('initial');
            $table->integer('emails_fetched')->default(0);
            $table->integer('emails_processed')->default(0);
            $table->string('last_message_id')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            // Indexes for efficient queries
            $table->index(['user_id', 'created_at']);
            $table->index(['sync_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gmail_syncs');
    }
};
