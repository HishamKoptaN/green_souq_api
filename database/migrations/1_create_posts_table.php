<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create(
            'posts',
            function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('parent_id')
                    ->nullable()
                    ->constrained('posts')
                    ->nullOnDelete();
                $table->enum('type', ['original', 'share'])->default('original');
                $table->text('content')->nullable();
                $table->unsignedInteger('likes_count')->default(0);
                $table->unsignedInteger('comments_count')->default(0);
                $table->unsignedInteger('shares_count')->default(0);
                $table->timestamps();
                $table->index(['user_id', 'created_at']);
                $table->index('parent_id');
            },
        );
    }

    public function down()
    {
        Schema::dropIfExists(
            'posts',
        );
    }
};
