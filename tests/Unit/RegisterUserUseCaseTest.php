<?php

namespace Tests\Unit;

use App\Features\Users\Domain\UseCases\RegisterUserUseCase;
use PHPUnit\Framework\TestCase;

class RegisterUserUseCaseTest extends TestCase
{
    public function test_can_instantiate_register_user_use_case(): void
    {
        $useCase = new RegisterUserUseCase();
        $this->assertInstanceOf(RegisterUserUseCase::class, $useCase);
    }
}
