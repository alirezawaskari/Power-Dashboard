<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('telemetry_rollups_5m', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete();
            $table->timestampTz('bucket_start_ts'); // aligned to 5-min boundaries
            // aggregates
            $table->double('min_power');
            $table->double('avg_power');
            $table->double('max_power');
            $table->unsignedInteger('count');
            // optional aggregates for current/voltage
            $table->double('min_current')->nullable();
            $table->double('avg_current')->nullable();
            $table->double('max_current')->nullable();
            $table->double('min_voltage')->nullable();
            $table->double('avg_voltage')->nullable();
            $table->double('max_voltage')->nullable();
            $table->timestamps();

            $table->unique(['device_id', 'bucket_start_ts']);
            $table->index(['device_id', 'bucket_start_ts']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telemetry_rollups_5m');
    }
};