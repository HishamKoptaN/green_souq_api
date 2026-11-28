<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasIndex('users', 'users_user_type_unique')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_user_type_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unique('user_type');
        });
    }
};
