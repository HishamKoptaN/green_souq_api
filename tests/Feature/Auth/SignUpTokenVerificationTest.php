<?php

namespace Tests\Feature\Auth;

use App\Features\Firebase\Domain\UseCases\FirebaseUseCases;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Mockery;
use Tests\TestCase;

class SignUpTokenVerificationTest extends TestCase
{
    private const PAYLOAD = [
        'name' => 'Test Guest',
        'phone' => '',
        'user_type' => 'shop',
        'id_token' => 'fake-token',
    ];

    public function test_invalid_id_token_returns_clear_unauthorized_response(): void
    {
        $fake = Mockery::mock(FirebaseUseCases::class);
        $fake->shouldReceive('verifiedIdToken')
            ->once()
            ->andThrow(new FailedToVerifyToken('expired'));
        $this->app->instance(FirebaseUseCases::class, $fake);

        $response = $this->postJson('/api/auth/sign-up', self::PAYLOAD);

        $response->assertStatus(401)
            ->assertJsonPath('error_code', 'INVALID_ID_TOKEN')
            ->assertJsonPath('message', 'رمز الدخول غير صالح أو منتهي الصلاحية، الرجاء تسجيل الدخول مرة أخرى');
    }

    public function test_unexpected_verification_failure_returns_clear_server_response(): void
    {
        $fake = Mockery::mock(FirebaseUseCases::class);
        $fake->shouldReceive('verifiedIdToken')
            ->once()
            ->andThrow(new \RuntimeException('credentials missing'));
        $this->app->instance(FirebaseUseCases::class, $fake);

        $response = $this->postJson('/api/auth/sign-up', self::PAYLOAD);

        $response->assertStatus(500)
            ->assertJsonPath('error_code', 'TOKEN_VERIFICATION_FAILED')
            ->assertJsonPath('message', 'تعذّر التحقق من حسابك، الرجاء المحاولة مرة أخرى');
        $this->assertStringNotContainsString('Server Error', $response->getContent());
    }
}
