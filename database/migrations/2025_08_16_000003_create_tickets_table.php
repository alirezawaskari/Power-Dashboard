<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // creator
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('device_id')->nullable()->constrained('devices')->nullOnDelete();
            $table->string('status')->default('open')->index(); // open|pending|closed
            $table->string('priority')->default('normal')->index(); // low|normal|high|urgent
            $table->string('thread_mode')->default('snapshot_json'); // client_only|snapshot_json
            $table->string('title');
            $table->timestamp('last_activity_at')->nullable()->index();
            $table->longText('snapshot_json')->nullable(); // JSON blob of thread
            $table->unsignedInteger('snapshot_version')->default(0); // optimistic concurrency for snapshot
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['assignee_id']);
            $table->index(['device_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
