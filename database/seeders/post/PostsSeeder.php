<?php

/*
php artisan db:seed --class="Database\\Seeders\\Social\\Post\\PostsSeeder"
*/

namespace Database\Seeders\Social\Post;

use Illuminate\Database\Seeder;

class PostsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            OccasionSeeder::class,
            PollPostSeeder::class,
            ServiceRequestSeeder::class,
            SharedPostSeeder::class,
            SocialPostSeeder::class,
        ]);
    }
}
