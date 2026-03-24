<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('url_caches', function (Blueprint $table) {
            $table->id();
            $table->text('url'); // URL penuh, tak limit
            $table->char('url_hash', 64)->unique(); // SHA-256
            $table->string('status'); // 'malicious', 'safe', 'unknown'
            $table->json('threat_types')->nullable();
            $table->timestamp('last_checked');
            $table->text('explanation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('url_caches');
    }
};

