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
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('message_id', 255)->unique();
            $table->timestamp('date')->nullable();
            $table->string('from', 255)->nullable();
            $table->string('subject', 255)->nullable();
            $table->text('snippet')->nullable();
            $table->enum('processing_status', ['fetched', 'scanned', 'deleted'])->default('fetched');
            $table->timestamps();

            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};
