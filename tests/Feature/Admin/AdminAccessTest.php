<?php

namespace Tests\Feature\Admin;

use App\Features\Admin\Data\Models\Role;
use App\Features\Firebase\Domain\UseCases\FirebaseUseCases;
use App\Features\Users\Data\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Mockery;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
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
    }

    public function test_guest_cannot_open_admin_area(): void
    {
        $this->getJson('/api/admin/me')->assertStatus(401);
    }

    public function test_regular_user_is_forbidden(): void
    {
        $user = User::create(['firebase_uid' => 'regular-uid', 'name' => 'مستخدم عادي']);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/admin/me')
            ->assertStatus(403)
            ->assertJsonPath('error_code', 'ADMIN_FORBIDDEN');
    }

    public function test_admin_can_open_admin_area(): void
    {
        $user = User::create(['firebase_uid' => 'admin-uid', 'name' => 'مشرف']);
        $user->roles()->attach(Role::create(['name' => 'admin']));

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/admin/me')
            ->assertOk()
            ->assertJsonPath('roles.0', 'admin');
    }

    public function test_admin_login_issues_admin_token(): void
    {
        $user = User::create(['firebase_uid' => 'admin-uid', 'name' => 'مشرف']);
        $user->roles()->attach(Role::create(['name' => 'super-admin']));

        $fake = Mockery::mock(FirebaseUseCases::class);
        $fake->shouldReceive('verifiedIdToken')->once()->andReturn('admin-uid');
        $this->app->instance(FirebaseUseCases::class, $fake);

        $this->postJson('/api/admin/login', ['id_token' => 'fake'])
            ->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'roles']]);
    }

    public function test_invalid_id_token_is_rejected(): void
    {
        $fake = Mockery::mock(FirebaseUseCases::class);
        $fake->shouldReceive('verifiedIdToken')->once()->andThrow(new FailedToVerifyToken('expired'));
        $this->app->instance(FirebaseUseCases::class, $fake);

        $this->postJson('/api/admin/login', ['id_token' => 'fake'])
            ->assertStatus(401)
            ->assertJsonPath('error_code', 'ADMIN_INVALID_TOKEN');
    }
}
