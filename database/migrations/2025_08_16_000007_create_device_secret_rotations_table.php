<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('device_secret_rotations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete();
            $table->string('secret_hash');
            $table->timestamp('rotated_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_secret_rotations');
    }
};