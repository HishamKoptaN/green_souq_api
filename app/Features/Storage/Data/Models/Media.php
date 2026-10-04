<?php

namespace App\Features\Storage\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'mediable_type',
        'mediable_id',
        'file_path',
        'disk',
        'file_type',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }
}
