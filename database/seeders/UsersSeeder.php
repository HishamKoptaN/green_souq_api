<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Features\Users\Data\Models\User;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['shop', 'salesEngineer', 'company', 'farmer'];
        foreach ($types as $type) {
            User::create([
                'name' => ucfirst($type) . ' User',
                'user_type' => $type,
                'phone' => '123456789' . rand(0, 9),
                'address' => 'mock address for ' . $type,
                'latitude' => 30.0444 + (rand(0, 100) / 10000),
                'longitude' => 31.2357 + (rand(0, 100) / 10000),
            ]);
        }
    }
}
