<?php

namespace App\Features\Posts\Present\Controllers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LikesController
{
    public function store(Request $request)
    {
        $modelClass = Relation::getMorphedModel($request['likeable_type']);
        $targetEntity = $modelClass::findOrFail($request['likeable_id']);
        if (method_exists($targetEntity, 'isDirectShare') && $targetEntity->isDirectShare() && $targetEntity->parent_id) {
            $targetEntity = $modelClass::find($targetEntity->parent_id) ?? $targetEntity;
        }
        $user = $request->user();
        $isLiked = DB::transaction(function () use ($targetEntity, $user) {
            $like = $targetEntity->likes()->where('user_id', $user->id)->first();
            if ($like) {
                $like->delete();
                $targetEntity->decrement('likes_count');

                return false;
            }
            $targetEntity->likes()->create([
                'user_id' => $user->id,
            ]);
            $targetEntity->increment('likes_count');

            return true;
        });

        return successRes();
    }
}
