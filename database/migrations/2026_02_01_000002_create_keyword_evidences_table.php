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
        Schema::create('keyword_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detection_result_id')->constrained('detection_results')->onDelete('cascade');
            $table->string('category'); // high_urgency, credential_request, financial, etc.
            $table->json('keywords_found'); // Array of specific keywords found
            $table->integer('count')->default(0); // Number of suspicious keywords found
            $table->text('explanation'); // Why these keywords are suspicious
            $table->enum('classification', ['phishing', 'legitimate', 'suspicious'])->default('phishing');
            $table->timestamps();

            $table->index('detection_result_id');
            $table->index('category','kw_cat_idx');
            $table->index('classification', 'kw_class_idx');
            $table->index(['detection_result_id', 'classification'], 'kw_result_class_idx');
            $table->index(['detection_result_id', 'classification', 'category'],'kw_full_idx');
            $table->index(['created_at', 'classification'],'kw_time_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keyword_evidences');
    }
};
