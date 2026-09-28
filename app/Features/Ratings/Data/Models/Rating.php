<?php

namespace App\Features\Ratings\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Features\Users\Data\Models\User;
class Rating extends Model
{
    protected $fillable = [
        'evaluator_id',
        'evaluated_id',
        'rating',
        'comment',
    ];
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
    public function evaluated(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_id');
    }
}