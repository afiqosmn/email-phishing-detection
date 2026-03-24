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
        Schema::create('html_anomaly_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detection_result_id')->constrained('detection_results')->onDelete('cascade');
            $table->string('anomaly_type'); // iframe, hidden_content, mismatched_links, etc.
            $table->enum('severity', ['low', 'medium', 'high'])->default('medium');
            $table->text('explanation'); // Detailed explanation of anomaly
            $table->json('metadata')->nullable(); // Additional context (count, location, etc.)
            $table->enum('classification', ['phishing', 'legitimate', 'suspicious'])->default('phishing');
            $table->timestamps();

            $table->index('detection_result_id');
            $table->index('anomaly_type','html_type_idx');
            $table->index('severity', 'html_sev_idx');
            $table->index('classification', 'html_class_idx');
            $table->index(['detection_result_id', 'classification'], 'html_result_class_idx');
            $table->index(['detection_result_id', 'classification', 'severity'], 'html_full_idx');
            $table->index(['created_at', 'classification'],'html_time_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('html_anomaly_evidences');
    }
};
