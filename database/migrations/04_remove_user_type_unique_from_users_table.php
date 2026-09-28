<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            try {
                $table->dropUnique('users_user_type_unique');
            } catch (Throwable $e) {
                // In case the index name is different or doesn't exist
                try {
                    $table->dropUnique(['user_type']);
                } catch (Throwable $ex) {
                    // Ignore if already dropped or doesn't exist
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
