<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detection_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_id')->nullable()->constrained('emails')->nullOnDelete();
            $table->string('message_id', 255)->nullable();
            $table->string('rule_result', 50)->nullable();
            $table->integer('rule_score')->default(0);
            $table->json('rule_details')->nullable();
            $table->string('ml_result', 50)->nullable();
            $table->string('final_decision', 50)->nullable();
            $table->decimal('ml_confidence', 5, 2)->nullable();
            $table->timestamps();

            $table->index('final_decision', 'dr_decision_idx');
            $table->index(['email_id', 'final_decision'], 'dr_email_dec_idx');
            $table->index(['created_at', 'final_decision'], 'dr_time_dec_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detection_results');
    }
};
