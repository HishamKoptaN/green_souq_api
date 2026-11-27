<?php

namespace Tests\Feature\Auth;

use App\Features\Firebase\Domain\UseCases\FirebaseUseCases;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\TestCase;

class SignUpUserTypeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firebase_uid')->unique()->nullable();
            $table->string('name')->nullable();
            $table->string('user_type')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('avatar')->nullable();
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

        $fake = Mockery::mock(FirebaseUseCases::class);
        $fake->shouldReceive('verifiedIdToken')->andReturn('signup-uid');
        $this->app->instance(FirebaseUseCases::class, $fake);
    }

    public function test_all_app_user_types_are_accepted(): void
    {
        foreach (['shop', 'salesEngineer', 'farmer', 'agriculturalToolsMerchant', 'jobSeeker'] as $type) {
            $this->postJson('/api/auth/sign-up', [
                'name' => 'اسم',
                'phone' => '',
                'user_type' => $type,
                'id_token' => 'fake',
            ])->assertStatus(201);
        }

        $this->assertDatabaseHas('users', [
            'firebase_uid' => 'signup-uid',
            'user_type' => 'jobSeeker',
        ]);
    }
}
