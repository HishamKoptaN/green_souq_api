<?php

namespace Tests\Feature\Admin;

use App\Features\Admin\Data\Models\Role;
use App\Features\Complaints\Data\Models\Complaint;
use App\Features\Jobs\Data\Models\Job;
use App\Features\Notifications\Data\Models\Notification;
use App\Features\Users\Data\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminStatsTest extends TestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('user_type')->nullable();
            $table->timestamps();
        });
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('guard_name')->default('sanctum');
            $table->timestamps();
        });
        Schema::create('user_has_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('role_id');
            $table->timestamps();
        });
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
        (require database_path('migrations/3_support_tickets_table.php'))->up();
        (require database_path('migrations/2026_11_26_000002_create_moderation_tables.php'))->up();
        (require database_path('migrations/2026_11_26_000003_create_notification_tables.php'))->up();
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('postable_type');
            $table->unsignedBigInteger('postable_id');
            $table->timestamps();
        });
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluator_id');
            $table->string('rateable_type');
            $table->unsignedBigInteger('rateable_id');
            $table->unsignedTinyInteger('rating');
            $table->timestamps();
        });

        $this->admin = User::create(['name' => 'مشرف']);
        $this->admin->roles()->attach(Role::create(['name' => 'admin']));
    }

    public function test_stats_report_dashboard_metrics(): void
    {
        User::create(['name' => 'مزارع', 'user_type' => 'farmer']);
        Complaint::create(['title' => 'شكوى', 'description' => 'وصف']);
        Job::create(['title' => 'وظيفة', 'description' => 'وصف']);
        Notification::create(['title' => 'إشعار', 'body' => 'نص']);

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/admin/stats')
            ->assertOk()
            ->assertJsonPath('users', 2)
            ->assertJsonPath('farms', 1)
            ->assertJsonPath('complaints', 1)
            ->assertJsonPath('jobs', 1)
            ->assertJsonPath('unreadNotifications', 1);
    }

    public function test_activity_returns_recent_operations(): void
    {
        Complaint::create(['title' => 'شكوى حديثة', 'description' => 'وصف']);

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/admin/activity')
            ->assertOk()
            ->assertJsonPath('0.type', 'complaint')
            ->assertJsonPath('0.label', 'شكوى حديثة');
    }
}
