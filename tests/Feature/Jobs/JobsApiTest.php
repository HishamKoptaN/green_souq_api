<?php

namespace Tests\Feature\Jobs;

use App\Features\Admin\Data\Models\Role;
use App\Features\Jobs\Data\Models\Job;
use App\Features\Users\Data\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class JobsApiTest extends TestCase
{
    private User $user;

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
        (require database_path('migrations/2026_11_26_000002_create_moderation_tables.php'))->up();

        $this->user = User::create(['name' => 'مستخدم']);
    }

    public function test_authenticated_user_can_list_open_jobs(): void
    {
        Job::create([
            'title' => 'مهندس ري',
            'description' => 'عمل ميداني',
            'location' => 'الرياض',
            'company_name' => 'شركة زراعية',
            'status' => 'open',
        ]);
        Job::create([
            'title' => 'وظيفة مغلقة',
            'description' => 'منتهية',
            'status' => 'closed',
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/user/jobs')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.title', 'مهندس ري')
            ->assertJsonPath('data.0.companyName', 'شركة زراعية')
            ->assertJsonPath('data.0.status', 'open');
    }

    public function test_jobs_can_be_filtered_by_query(): void
    {
        Job::create(['title' => 'مهندس ري', 'description' => 'وصف', 'status' => 'open']);
        Job::create(['title' => 'فني ري', 'description' => 'وصف', 'status' => 'open']);

        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/user/jobs?query=فني')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.title', 'فني ري');
    }

    public function test_admin_create_requires_fields_and_saves_all_details(): void
    {
        $this->user->roles()->attach(Role::create(['name' => 'admin']));

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/admin/jobs', [])
            ->assertStatus(422)
            ->assertJsonStructure(['message']);

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/admin/jobs', [
                'title' => 'وظيفة جديدة',
                'description' => 'وصف',
                'location' => 'جدة',
                'company_name' => 'شركة',
                'status' => 'closed',
            ])
            ->assertCreated()
            ->assertJsonPath('status', 'closed');

        $this->assertDatabaseHas('jobs', [
            'title' => 'وظيفة جديدة',
            'company_name' => 'شركة',
            'status' => 'closed',
        ]);
    }
}
