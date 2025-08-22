<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type'); // e.g., ingest.accepted, device.offline
            // actor (nullable polymorphic)
            $table->string('actor_type')->nullable(); // user|device|system
            $table->unsignedBigInteger('actor_id')->nullable();
            // subject (polymorphic)
            $table->string('subject_type'); // device|ticket|power_record|user|rule|alert
            $table->unsignedBigInteger('subject_id');
            $table->string('message');
            $table->json('context')->nullable();
            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();

            $table->index(['subject_type', 'subject_id', 'occurred_at']);
            $table->index(['type', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_logs');
    }
};