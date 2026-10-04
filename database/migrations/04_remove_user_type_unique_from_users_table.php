<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            try {
                $table->dropUnique('users_user_type_unique');
            } catch (Throwable $e) {
                try {
                    $table->dropUnique(['user_type']);
                } catch (Throwable $ex) {
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unique('user_type');
        });
    }
};
