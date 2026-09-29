<?php

namespace Database\Seeders\Social\Post;

use App\Models\Social\Post\Poll;
use App\Models\Social\Post\Post;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;

class PollPostSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create();
        $pollIds = [2, 7, 12, 17, 22];
        foreach ($pollIds as $id) {
            $poll = Poll::updateOrCreate(
                ['id' => $id],
                [
                    'question' => $faker->sentence,
                    'end_at' => now()->addDays(3),
                ]
            );
            Post::updateOrCreate(
                ['postable_id' => $poll->id, 'postable_type' => 'poll'],
                [
                    'user_id' => $faker->numberBetween(1, 10),
                    'publish_at' => $faker->dateTimeBetween('-3 days', 'now'),
                ]
            );
        }
    }
}
