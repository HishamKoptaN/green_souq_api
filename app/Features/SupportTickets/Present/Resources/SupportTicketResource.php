<?php

namespace App\Features\SupportTickets\Present\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupportTicketResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'subject' => $this->subject,
            'type' => $this->type,
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
