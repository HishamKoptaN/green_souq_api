<?php

namespace App\Features\Ratings\Data\Models;

use App\Features\Users\Data\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Rating extends Model
{
    protected $fillable = [
        'evaluator_id',
        'rateable_type',
        'rateable_id',
        'rating',
        'comment',
    ];

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function rateable(): MorphTo
    {
        return $this->morphTo();
    }
}
