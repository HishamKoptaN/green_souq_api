<?php

namespace App\Features\Jobs\Present\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => (string) $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location ?? '',
            'companyName' => $this->company_name ?? '',
            'status' => $this->status ?? 'open',
            'createdAt' => $this->created_at?->toIso8601String(),
        ];
    }
}
