<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // NotificationType enum
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Additional context data
            $table->string('status')->default('pending'); // NotificationStatus enum
            $table->string('channel')->default('websocket'); // email, sms, push, websocket
            $table->integer('priority')->default(0); // Higher = more important
            $table->integer('retry_count')->default(0);
            $table->integer('max_retries')->default(3);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('scheduled_at')->nullable(); // For scheduled notifications
            $table->timestamp('expires_at')->nullable(); // TTL for notifications
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'read_at']);
            $table->index(['status', 'scheduled_at']);
            $table->index(['type', 'created_at']);
            $table->index(['expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
