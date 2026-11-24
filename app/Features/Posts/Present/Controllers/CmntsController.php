<?php

namespace App\Features\Posts\Present\Controllers;

use App\Events\NotificationSent;
use App\Features\Posts\Data\Models\Comment;
use App\Features\Posts\Data\Models\SocialPost;
use App\Features\Posts\Present\Resources\CmntResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CmntsController
{
    public function index(Request $request)
    {
        try {
            $comments = Comment::where('commentable_id', $request->commentable_id)
                ->where('commentable_type', $request->commentable_type)
                ->with(['user'])
                ->latest()
                ->paginate(5);

            return successRes(
                paginateRes(
                    $comments,
                    CmntResource::class,
                ),
            );
        } catch (\Exception $e) {
            report($e);

            return failureRes('تعذّر تحميل التعليقات، الرجاء المحاولة مرة أخرى');
        }
    }

    public function show($id)
    {
        try {
            $comment = Comment::find($id);
            if (! $comment) {
                return response()->json([
                    'error' => 'Comment not found',
                ], 404);
            }

            return successRes(new CmntResource($comment), 200);
        } catch (\Exception $e) {
            report($e);

            return failureRes('تعذّر تحميل التعليق، الرجاء المحاولة مرة أخرى');
        }
    }

    public function store(
        Request $request,
    ) {
        try {
            SocialPost::find($request->commentable_id);
            $comment = Comment::create([
                'comment' => $request->comment,
                'user_id' => Auth::id(),
                'commentable_id' => $request->commentable_id,
                'commentable_type' => 'social',
            ]);

            // event(
            //    new NotificationSent(
            //        userId: $post->user_id,
            //        title: 'تعليق جديد',
            //        body: "{$$user->name} قام بالتعليق على منشورك!",
            //        image: $user->userable->image,
            //        data: [
            //            'key' => 'value',
            //        ]
            //    ),
            // );
            return successRes(new CmntResource($comment->fresh()), 201);
        } catch (\Exception $e) {
            report($e);

            return failureRes('تعذّر إضافة التعليق، الرجاء المحاولة مرة أخرى');
        }
    }
}
