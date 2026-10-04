<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create(
            'support_tickets',
            function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('subject');
                $table->string('type');
                $table->text('description');
                $table->enum('status', ['open', 'closed'])->default('open');
                $table->timestamps();
            },
        );
    }

    public function down()
    {
        Schema::dropIfExists('comments');
    }
};
