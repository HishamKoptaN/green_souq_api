<?php

namespace Tests\Feature\Auth;

use App\Features\Firebase\Domain\UseCases\FirebaseUseCases;
use App\Features\Profile\Present\Controllers\ProfileUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class LocalizedExceptionResponseTest extends TestCase
{
    public function test_profile_failure_does_not_expose_the_raw_exception(): void
    {
        $user = Mockery::mock();
        $user->shouldReceive('toArray')->andThrow(new \RuntimeException('secret database detail'));
        $guard = Mockery::mock();
        $guard->shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('guard')->with('sanctum')->andReturn($guard);

        $controller = new ProfileUserController(Mockery::mock(FirebaseUseCases::class));
        $response = $controller->index(Request::create('/api/user/profile'));

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame(
            'تعذّر تحميل البيانات الشخصية، الرجاء المحاولة مرة أخرى',
            $response->getData(true)['message'],
        );
        $this->assertStringNotContainsString('secret database detail', $response->getContent());
    }
}
