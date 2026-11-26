<?php

namespace Tests\Feature\Admin;

use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;
use Illuminate\Support\Facades\Schema;

class AdminTablesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
        });
    }

    public function test_admin_migrations_create_missing_tables(): void
    {
        foreach ([
            '2026_11_26_000002_create_moderation_tables.php',
            '2026_11_26_000003_create_notification_tables.php',
        ] as $file) {
            (require database_path('migrations/'.$file))->up();
        }

        foreach (['complaints', 'suggestions', 'jobs', 'reviews', 'notifications', 'notification_user', 'fcm_tokens'] as $table) {
            $this->assertTrue(Schema::hasTable($table), "missing table: {$table}");
        }
    }
}
