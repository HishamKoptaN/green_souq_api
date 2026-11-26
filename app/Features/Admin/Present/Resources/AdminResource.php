<?php

namespace App\Features\Admin\Present\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    public function toArray($request): array
    {
        return parent::toArray($request);
    }
}
