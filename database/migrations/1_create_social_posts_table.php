<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create(
            'social_posts',
            function (Blueprint $table) {
                $table->id();
                $table->text('content')->nullable();
                $table->timestamps();
            },
        );
    }

    public function down()
    {
        Schema::dropIfExists(
            'social_posts',
        );
    }
};
