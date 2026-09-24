<?php

namespace App\Features\Shops\Present\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'user_type' => $this->user_type,
            'phone' => $this->phone,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}
