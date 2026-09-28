<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluator_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('evaluated_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['evaluator_id', 'evaluated_id'], 'unique_user_rating_pair');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};