<?php

namespace Database\Seeders\Social\Post;

use App\Models\Social\Post\Post;
use App\Models\Social\Post\SharedPost;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;

class SharedPostSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create();
        $sharedPostIds = [4, 9, 14, 19, 24];
        foreach ($sharedPostIds as $id) {
            $originalPost = Post::inRandomOrder()->first();
            $sharedPost = SharedPost::updateOrCreate(
                ['id' => $id],
                [
                    'post_id' => $originalPost->id,
                    'content' => $faker->sentence,
                ]
            );

            Post::updateOrCreate(
                ['postable_id' => $sharedPost->id, 'postable_type' => 'shared_post'],
                [
                    'user_id' => $faker->numberBetween(1, 10),
                    'publish_at' => $faker->dateTimeBetween('-3 days', 'now'),
                ]
            );
        }
    }
}
