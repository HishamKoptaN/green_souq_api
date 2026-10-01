<?php

namespace App\Features\Users\Data\Models;

use App\Features\Ratings\Data\Models\Rating;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['firebase_uid', 'name', 'bio', 'user_type', 'phone', 'address', 'latitude', 'longitude'])]
#[Hidden(['remember_token'])]
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $appends = ['average_rating', 'ratings_count'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected function avatar(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (! $value) {
                    return null;
                }

                return Storage::disk('public')->url($value);
            }
        );
    }

    public function averageRating(): float
    {
        return (float) ($this->receivedRatings()->avg('rating') ?? 3.0);
    }

    public function ratingsCount(): int
    {
        return (int) $this->receivedRatings()->count();
    }

    public function getAverageRatingAttribute(): float
    {
        return $this->averageRating();
    }

    public function getRatingsCountAttribute(): int
    {
        return $this->ratingsCount();
    }

    public function givenRating()
    {
        return $this->hasMany(Rating::class, 'evaluator_id');
    }

    public function receivedRatings()
    {
        return $this->hasMany(Rating::class, 'evaluated_id');
    }
}
