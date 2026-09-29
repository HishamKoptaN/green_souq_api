<?php

namespace Database\Seeders\Social\Post;

use App\Models\Social\Post\Comment;
use Illuminate\Database\Seeder;

class CmntSeeder extends Seeder
{
    public function run()
    {
        Comment::factory(100)->create();
    }
}
