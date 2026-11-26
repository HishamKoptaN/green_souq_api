<?php

namespace Database\Seeders;

use App\Features\Admin\Data\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['admin', 'super-admin'] as $name) {
            Role::firstOrCreate(['name' => $name], ['guard_name' => 'sanctum']);
        }
    }
}
