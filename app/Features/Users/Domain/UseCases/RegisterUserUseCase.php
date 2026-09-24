<?php

namespace App\Features\Users\Domain\UseCases;

use App\Features\Users\Data\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterUserUseCase
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'user_type' => $data['user_type'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
            ]);
            return $user;
        });
    }
}
