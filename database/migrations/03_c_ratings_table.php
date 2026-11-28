<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ratings')) {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluator_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->morphs('rateable');
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['evaluator_id', 'rateable_type', 'rateable_id'], 'unique_evaluator_rateable_pair');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
