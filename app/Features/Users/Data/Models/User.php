<?php

namespace App\Features\Users\Data\Models;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Features\Ratings\Data\Models\Rating;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['firebase_uid', 'name', 'user_type', 'phone', 'address', 'latitude', 'longitude'])]
#[Hidden(['remember_token'])]
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function givenRating()
    {
        return $this->hasMany(Rating::class, 'evaluator_id');
    }
    public function receivedRatings()
    {
        return $this->hasMany(Rating::class, 'evaluated_id');
    }
    public function averageRating()
    {
        return (float) $this->receivedRatings()->avg('rating') ?: 3.0;
    }
  
}
