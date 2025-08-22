<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('status')->default('offline')->index(); // online|offline|maintenance|decommissioned
            $table->string('secret_hash'); // store hash, never plaintext
            $table->string('firmware')->nullable();
            $table->string('model')->nullable();
            $table->string('location')->nullable();
            $table->json('tags')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->unsignedInteger('heartbeat_seconds')->nullable();
            $table->timestamp('last_rotated_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};