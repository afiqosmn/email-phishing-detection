<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('email_id')->constrained('emails')->onDelete('cascade');
            $table->enum('report_type', [
                'false_positive',      // System flagged as phishing, but user says legitimate
                'false_negative',      // System missed it, user flagged as phishing
                'unrequested_phishing', // User reports suspicious email regardless of system result
                'whitelist_request',   // User requests to whitelist/trust this sender
                'other'                // Generic report
            ]);
            $table->text('reason')->nullable(); // User's explanation/notes
            $table->enum('status', [
                'submitted',     // Newly submitted
                'reviewed',      // Security team reviewed
                'acknowledged',  // Action taken
                'dismissed'      // No action needed
            ])->default('submitted');
            $table->text('admin_notes')->nullable(); // Security team's response/notes
            $table->timestamps();

            // Indexes for efficient querying
            $table->index('user_id');
            $table->index('email_id');
            $table->index('status');
            $table->index('report_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_reports');
    }
};
