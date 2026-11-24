<?php

namespace Tests\Feature\Auth;

use App\Features\Firebase\Domain\UseCases\FirebaseUseCases;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Mockery;
use Tests\TestCase;

class AuthTokenVerificationTest extends TestCase
{
    public function test_invalid_id_token_returns_401_instead_of_500(): void
    {
        $fake = Mockery::mock(FirebaseUseCases::class);
        $fake->shouldReceive('verifiedIdToken')
            ->once()
            ->andThrow(new FailedToVerifyToken('expired'));
        $this->app->instance(FirebaseUseCases::class, $fake);

        $response = $this->postJson('/api/auth/auth-token', ['id_token' => 'fake-token']);

        $response->assertStatus(401)
            ->assertJsonPath('message', 'فشلت عملية تسجيل الدخول')
            ->assertJsonPath('error_code', 'AUTH_ERROR');
    }

    public function test_unexpected_failure_still_returns_500(): void
    {
        $fake = Mockery::mock(FirebaseUseCases::class);
        $fake->shouldReceive('verifiedIdToken')
            ->once()
            ->andThrow(new \RuntimeException('boom'));
        $this->app->instance(FirebaseUseCases::class, $fake);

        $response = $this->postJson('/api/auth/auth-token', ['id_token' => 'fake-token']);

        $response->assertStatus(500)
            ->assertJsonPath('message', 'فشلت عملية تسجيل الدخول')
            ->assertJsonPath('error_code', 'AUTH_ERROR');
    }
}
