<?php

namespace App\Models\Social\Post;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'question',
        'end_at',
    ];

    public function posts()
    {
        return $this->morphMany(
            Post::class,
            'postable',
        );
    }

    public function options()
    {
        return $this->hasMany(
            PollOption::class,
        );
    }
}
