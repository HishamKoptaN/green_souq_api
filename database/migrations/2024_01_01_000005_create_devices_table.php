<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('device_id');
            $table->string('device_type', 20);
            $table->string('model_name')->nullable();
            $table->string('os_version')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
            $table->unique([
                'user_id',
                'device_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
