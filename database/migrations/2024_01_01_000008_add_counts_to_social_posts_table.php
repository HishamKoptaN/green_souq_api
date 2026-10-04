<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('social_posts', function (Blueprint $table) {
            if (!Schema::hasColumn('social_posts', 'likes_count')) {
                $table->unsignedBigInteger('likes_count')->default(0)->after('content');
            }
            if (!Schema::hasColumn('social_posts', 'comments_count')) {
                $table->unsignedBigInteger('comments_count')->default(0)->after('likes_count');
            }
            if (!Schema::hasColumn('social_posts', 'shares_count')) {
                $table->unsignedBigInteger('shares_count')->default(0)->after('comments_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('social_posts', function (Blueprint $table) {
            $table->dropColumn(['likes_count', 'comments_count', 'shares_count']);
        });
    }
};
