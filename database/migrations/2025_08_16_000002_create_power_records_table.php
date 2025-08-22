<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('power_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // denormalized for filtering
            $table->timestampTz('ts'); // reading timestamp (UTC preferred)
            $table->double('current');
            $table->double('voltage');
            $table->double('power'); // computed on write: current*voltage
            $table->unsignedInteger('sampling_ms')->default(1000);
            $table->string('phase')->nullable();
            $table->json('flags')->nullable();
            $table->timestamps();

            $table->index(['device_id', 'ts']);
            $table->index(['user_id', 'ts']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('power_records');
    }
};