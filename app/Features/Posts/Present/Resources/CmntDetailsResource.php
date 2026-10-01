<?php

namespace App\Http\Resources\Social\Cmnts;

use App\Http\Resources\Social\Post\PostResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CmntDetailsResource extends JsonResource
{
    public function toArray(
        $request,
    ) {
        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'user' => $this->getAuthorDetails(),
            'post' => new PostResource($this->post),
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }

    private function getAuthorDetails()
    {
        $author = optional($this->user)->userable;

        return [
            'type' => $author->getMorphClass(),
            'name' => $author->name,
            'image' => $author->image,
        ];
    }
}
