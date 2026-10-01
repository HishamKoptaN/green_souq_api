<?php

namespace App\Features\Posts\Present\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CmntResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'author' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'image' => $this->user?->image,
            ],
            'isAuthor' => optional($this->user)->id === Auth::guard('sanctum')->user()->id,
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
