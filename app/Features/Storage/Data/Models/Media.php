<?php

namespace App\Features\Storage\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
    protected function filePath(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value) {
                    return null;
                }
                return Storage::disk($this->disk)->url($value);
            }
        );
    }
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }
}
