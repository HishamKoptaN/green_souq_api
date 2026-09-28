<?php

namespace App\Features\SalesEngineers\Present\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesEngineerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'user_type' => $this->user_type,
            'phone' => $this->phone,
            'rating' => $this->receivedRatings->first()?->rating ?? null,
            'rating_average' => $this->averageRating(),
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}
