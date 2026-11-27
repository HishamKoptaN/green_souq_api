<?php

namespace Tests\Feature\Auth;

use App\Features\Firebase\Domain\UseCases\FirebaseUseCases;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class AuthCheckFailureResponseTest extends TestCase
{
    public function test_unauthenticated_check_exposes_the_reason_in_message(): void
    {
        $this->app->instance(FirebaseUseCases::class, Mockery::mock(FirebaseUseCases::class));

        $response = $this->postJson('/api/auth/check');

        $response->assertStatus(401)
            ->assertJsonPath('message', 'غير مصرح بالدخول')
            ->assertJsonPath('errors', null);
    }

    public function test_failed_check_keeps_arabic_message_and_technical_error(): void
    {
        $this->app->instance(FirebaseUseCases::class, Mockery::mock(FirebaseUseCases::class));
        Auth::shouldReceive('guard')->andThrow(new \RuntimeException('guard unavailable'));

        $response = $this->postJson('/api/auth/check');

        $response->assertStatus(500)
            ->assertJsonPath('message', 'حدث خطأ أثناء التحقق من المصادقة')
            ->assertJsonPath('errors', 'guard unavailable');
    }
}
