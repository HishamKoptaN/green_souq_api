<?php

namespace Database\Seeders\Social\Post;

use App\Models\Social\Post\Post;
use App\Models\Social\Post\ServiceRequest;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;

class ServiceRequestSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create();
        $requestIds = [3, 8, 13, 18, 23];
        foreach ($requestIds as $id) {
            $request = ServiceRequest::updateOrCreate(
                ['id' => $id],
                [
                    'title' => $faker->sentence,
                    'specialization_id' => rand(1, 10),
                    'details' => $faker->paragraph,
                ]
            );
            Post::updateOrCreate(
                ['postable_id' => $request->id, 'postable_type' => 'serv_req'],
                [
                    'user_id' => $faker->numberBetween(1, 10),
                    'publish_at' => $faker->dateTimeBetween('-3 days', 'now'),
                ]
            );
        }
    }
}
