<?php

namespace App\Features\Shops\Data\Models;

use App\Features\Users\Data\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Shop extends Model
{
    protected $fillable = [
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
