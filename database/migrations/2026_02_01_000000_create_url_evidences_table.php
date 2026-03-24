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
        Schema::create('url_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detection_result_id')->constrained('detection_results')->onDelete('cascade');
            $table->longText('url'); // URLs can be long
            $table->enum('status', ['safe', 'malicious', 'suspicious', 'unknown'])->default('unknown');
            $table->json('threat_types')->nullable(); // ['malware', 'phishing', etc.]
            $table->text('explanation'); // Why it's safe/malicious
            $table->string('source')->default('google_safe_browsing'); // API source
            $table->enum('classification', ['phishing', 'legitimate', 'suspicious'])->default('phishing');
            $table->timestamps();

            $table->index('detection_result_id');
            $table->index('status','url_status_idx');
            $table->index('classification', 'url_class_idx');
            $table->index(['detection_result_id', 'classification'], 'url_result_class_idx');
            $table->index(['detection_result_id', 'classification', 'status'],'url_full_idx');
            $table->index(['created_at', 'classification'],'url_time_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('url_evidences');
    }
};
