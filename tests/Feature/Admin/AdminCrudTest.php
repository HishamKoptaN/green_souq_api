<?php

namespace Tests\Feature\Admin;

use App\Features\Admin\Data\Models\Role;
use App\Features\Users\Data\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firebase_uid')->unique()->nullable();
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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluator_id');
            $table->string('rateable_type');
            $table->unsignedBigInteger('rateable_id');
            $table->unsignedTinyInteger('rating');
            $table->timestamps();
        });
        (require database_path('migrations/2026_11_26_000002_create_moderation_tables.php'))->up();

        $this->admin = User::create(['name' => 'مشرف', 'user_type' => 'admin']);
        $this->admin->roles()->attach(Role::create(['name' => 'admin']));
    }

    public function test_admin_can_list_users(): void
    {
        User::create(['name' => 'مزارع', 'user_type' => 'farmer']);

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/admin/users?query=مزارع')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.name', 'مزارع');
    }

    public function test_admin_can_create_update_and_delete_job(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/admin/jobs', ['title' => 'مهندس ري', 'description' => 'عمل ميداني'])
            ->assertCreated()
            ->assertJsonPath('title', 'مهندس ري');

        $this->actingAs($this->admin, 'sanctum')
            ->putJson('/api/admin/jobs/1', ['title' => 'مهندس ري أول'])
            ->assertOk()
            ->assertJsonPath('title', 'مهندس ري أول');

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson('/api/admin/jobs/1')
            ->assertOk();
    }
}
