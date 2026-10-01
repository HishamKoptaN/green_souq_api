<?php

namespace App\Features\Posts\Present\Resources;

use App\Features\Posts\Data\Models\SocialPost;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PostResource extends JsonResource
{
    public function toArray(
        $request,
    ) {
        $postArray = [
            'id' => $this->id,
            'type' => $this->postable_type,
            'author' => $this->author,
            'postable' => new PostableResource(
                $this->whenLoaded(
                    'postable',
                ),
            ),

            // 'cmnters_images' => Comment::where('commentable_id', $this->id)
            // ->where('commentable_type', Post::class)
            // ->with('user.userable')
            // ->latest()
            // ->take(10)
            // ->get()
            // ->map(function ($comment) {
            // $user = $comment->user;
            // return [
            // 'id' => $user->id,
            // 'image' => optional($user->userable)->image,
            // ];
            // })
            // ->unique('id')
            // ->pluck('image')
            // ->filter()
            // ->values(),
            'created_at' => $this->created_at,
        ];

        return $postArray;
    }

    private function isLikedByUser()
    {
        $user = Auth::guard('sanctum')->user();

        return $user ? $this->likes()->where('user_id', $user->id)->exists() : false;
    }
}

class PostableResource extends JsonResource
{
    public function toArray($request)
    {
        return match (get_class($this->resource)) {
            SocialPost::class => new CustomPostResource($this),
            //   SharedPost::class => new SharedPostResource($this),
            //   Occasion::class => new OccasionResource($this),
            //   Poll::class => new PollResource($this),
            //   Draft::class => new CustomPostResource($this),
            //   CompanyPost::class => new CustomPostResource($this),
            //   News::class => new CustomPostResource($this),
            //   ServiceRequest::class => new ServiceRequestResource($this),
            default => new CustomPostResource($this),
        };
    }
}

class SharedPostResource extends JsonResource
{
    public function toArray(
        $request,
    ) {

        $sharedPost = Post::with('postable')->find(
            $this->post_id,
        );
        if ($sharedPost->postable instanceof Poll) {
            $sharedPost->postable->load('options');
        }
        $postArray = [
            'id' => $this->id,
            'type' => $this->type,
            'author' => $this->getAuthorDetails($this->user),
            'shared_post' => new PostResource(
                $sharedPost,
            ),
            'created_at' => $this->created_at->diffForHumans(),
            'publish_at' => $this->publish_at,
        ];

        return $postArray;
    }

    private function getAuthorDetails($user)
    {
        $authUser = Auth::guard('sanctum')->user();

        if (! $user || ! $user->userable) {
            return [
                'id' => null,
                'type' => 'Unknown',
                'name' => 'Anonymous',
                'image' => null,
                'is_following' => false,
            ];
        }
        $author = $user->userable;
        $isFollowing = $authUser && $authUser->id !== $user->id
            ? $authUser->following()->where('followed_id', $user->id)->exists()
            : null;

        return [
            'id' => $author->id,
            'type' => $author->getMorphClass(),
            'name' => $author->name,
            'image' => $author->image,
            'is_following' => $isFollowing,
        ];
    }
}
class CustomPostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'is_like' => $this->isLikedBy(),
            'likes_count' => $this->likes_count,
            'comments_count' => $this->comments_count,
            // 'shares_count' => $this->shares()->count(),
        ];
    }
}
class OccasionResource extends JsonResource
{
    public function toArray($request)
    {
        $authUser = Auth::guard('sanctum')->user();
        $occasion = Occasion::find($this->id);
        $isInterested = $authUser->isInterestedIn($occasion);

        return [
            'id' => $occasion->id,
            'image' => $occasion->image,
            'title' => $occasion->title,
            'description' => $occasion->description,
            'interested_count' => $occasion->interested_count,
            'is_interested' => $isInterested,
            'start_at' => Carbon::parse($occasion->start_at)->locale('ar')->translatedFormat('d F, Y g:i A'),
            'end_at' => Carbon::parse($occasion->end_at)->locale('ar')->translatedFormat('d F, Y g:i A'),
        ];
    }
}

class PollResource extends JsonResource
{
    public function toArray(
        $request,
    ) {
        $user = Auth::user();
        $selectedOptionId = $this->options()
            ->whereHas('votes', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->pluck('id')
            ->first();
        $this->when(
            $this->type === 'poll', // تحقق من نوع المنشور
            function () {
                return PollOptionResource::collection($this->whenLoaded('options'));
            },
            []
        );

        return [
            'id' => $this->id,
            'status' => $this->status,
            'question' => $this->question,
            'options' => PollOptionResource::collection(
                $this->whenLoaded(
                    'options',
                ),
            ),
            'selected_option' => $selectedOptionId,
            'end_at' => $this->end_at,
            'is_ended' => now()->greaterThan($this->end_at),
        ];
    }
}

class PollOptionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'option' => $this->option,
            'votes_count' => $this->votes,
        ];
    }
}
class ServiceRequestResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'specialization_id' => $this->specialization_id,
            'details' => $this->details,
        ];
    }
}
class UserDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'type' => $this->resource->getMorphClass(),
            'name' => $this->name,
            'image' => $this->image,
            'cover_image' => $this->cover_image,
        ];
    }
}
