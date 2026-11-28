<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('firebase_uid')->unique()->nullable();
                $table->string('avatar')->nullable();
                $table->string('name')->nullable();
                $table->string('phone')->nullable();
                $table->string('user_type')->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->timestamps();
            });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
