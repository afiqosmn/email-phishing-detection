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
        Schema::create('authentication_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detection_result_id')->constrained('detection_results')->onDelete('cascade');
            $table->enum('check_type', ['spf', 'dkim', 'dmarc']); // Type of authentication check
            $table->enum('result', ['pass', 'fail', 'neutral', 'none', 'unknown'])->default('unknown');
            $table->boolean('aligned')->nullable(); // Whether domain is aligned
            $table->text('explanation'); // Detailed explanation of result
            $table->enum('classification', ['phishing', 'legitimate', 'suspicious'])->default('phishing');
            $table->timestamps();

            $table->index('detection_result_id');
            $table->index('check_type','auth_type_idx');
            $table->index('classification', 'auth_class_idx');
            $table->index(['detection_result_id', 'classification'], 'auth_result_class_idx');
            $table->index(['detection_result_id', 'classification', 'created_at'],'auth_full_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authentication_evidences');
    }
};
