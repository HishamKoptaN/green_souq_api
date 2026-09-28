<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create(
            'post_shares',
            function (Blueprint $table) {},
        );
    }

    public function down()
    {
        Schema::dropIfExists('post_shares');
    }
};
