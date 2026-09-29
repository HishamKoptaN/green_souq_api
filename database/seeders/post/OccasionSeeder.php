<?php

/*
php artisan db:seed --class="Database\\Seeders\\Social\\Post\\OccasionSeeder"
*/

namespace Database\Seeders\Social\Post;

use App\Models\Social\Post\Occasion;
use App\Models\Social\Post\Post;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;

class OccasionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create();
        // معرفات ثابتة لمناسبات محددة
        $occasionIds = [1, 6, 11, 16, 21];
        foreach ($occasionIds as $id) {
            // إنشاء أو تحديث المناسبة
            $occasion = Occasion::updateOrCreate(
                ['id' => $id],
                [
                    'title' => $faker->sentence,
                    'description' => $faker->paragraph,
                    'link' => $faker->url,
                    'start_at' => $faker->dateTimeBetween('now', '+1 week'),
                    'end_at' => $faker->dateTimeBetween('+1 week', '+2 weeks'),
                    'image' => $faker->randomElement(
                        [
                            'https://res.cloudinary.com/dqzu6ln2h/image/upload/v1741171126/samples/cup-on-a-table.jpg',
                            'https://res.cloudinary.com/dqzu6ln2h/image/upload/v1741171124/samples/balloons.jpg',
                        ],
                    ),
                ]
            );
            // إنشاء أو تحديث المنشور المرتبط
            Post::updateOrCreate(
                ['postable_id' => $occasion->id, 'postable_type' => 'occasion'],
                [
                    'user_id' => $faker->numberBetween(1, 10),
                    'publish_at' => $faker->dateTimeBetween('-3 days', 'now'),
                ]
            );
        }
    }
}
