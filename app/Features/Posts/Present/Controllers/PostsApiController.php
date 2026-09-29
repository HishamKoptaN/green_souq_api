<?php

namespace App\Http\Controllers\Api\Social;

use App\Helpers\UploadFileHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Social\Post\DraftResource;
use App\Http\Resources\Social\Post\PostResource;
use App\Models\Social\Post\Draft;
use App\Models\Social\Post\Occasion;
use App\Models\Social\Post\Poll;
use App\Models\Social\Post\Post;
use App\Models\Social\Post\ServiceRequest;
use App\Models\Social\Post\SharedPost;
use App\Models\Social\Post\SocialPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostsApiController extends Controller
{
    public function get(
        Request $request,
    ) {
        try {
            $query = Post::with(
                [
                    'postable',
                    'user.userable',
                    'comments.user',
                ],
            );
            // فلترة حسب الجدولة
            if (filter_var($request->scheduled, FILTER_VALIDATE_BOOLEAN)) {
                $query->where('user_id', Auth::id())
                    ->where('publish_at', '>', now());
            } else {
                $query->where(function ($q) {
                    $q->whereNull('publish_at')
                        ->orWhere('publish_at', '<=', now());
                });
                // فلترة حسب "my_data"
                if (filter_var($request->my_data, FILTER_VALIDATE_BOOLEAN)) {
                    $query->where('user_id', Auth::id());
                } else {
                    $query->when($request->user_id, function ($q, $userId) {
                        return $q->where('user_id', $userId);
                    });
                }
                // فلترة حسب النوع (type)
                if ($request->has('type')) {
                    $types = $request->type;  // يأخذ الأنواع من query
                    if (is_array($types)) {
                        $query->whereIn('postable_type', $types);
                    } else {
                        $query->where('postable_type', $types);
                    }
                }
            }
            // جلب البيانات مع الترتيب الأحدث والتقسيم إلى صفحات
            $posts = $query->orderByDesc('publish_at')->latest()->paginate(5);
            // جلب المنشور المؤقت
            $draftPost = Draft::where('user_id', Auth::id())->first();
            // تحميل العلاقات الإضافية للـ Poll فقط
            $posts->getCollection()->each(function ($post) {
                if ($post->postable instanceof Poll) {
                    $post->postable->load('options');
                }
                if ($post->postable instanceof SharedPost && $post->postable->postable) {
                    $post->originalPost = new PostResource($post->postable->postable);
                }
            });

            // إرسال الاستجابة
            return successRes([
                'posts' => paginateRes($posts, PostResource::class, 'posts'),
                'draft_post' => $draftPost ? new DraftResource($draftPost) : null,
            ]);
        } catch (\Exception $e) {
            return failureRes($e->getMessage());
        }
    }

    private function createSocialPost(
        Request $request,
        $image,
        $pdf,
        $video,
        $thumbnail_url,
    ) {
        return SocialPost::create(
            [
                'content' => $request->input(
                    'content',
                ),
                'image' => $image,
                'video' => $video,
                'thumbnail_url' => $thumbnail_url,
                'pdf' => $pdf,
                'pdf_name' => $request->input(
                    'pdf_name',
                ),
            ],
        );
    }

    private function createSharedPost(
        Request $request,
    ) {
        return SharedPost::create(
            [
                'content' => $request->input(
                    'content',
                ),
                'post_id' => $request->input(
                    'post_id',
                ),
            ],
        );
    }

    private function createOccasion(
        Request $request,
        $user
    ) {
        $occasion = $request->input('occasion');
        $imageUpload = $this->handleUpload($request, $user, 'posts', 'occasion.image', $request->image_url);
        $imagePath = $imageUpload['file_url'] ?? null;

        return Occasion::create(
            [
                'title' => $occasion['title'] ?? '',
                'description' => $occasion['description'] ?? '',
                'link' => $occasion['link'] ?? '',
                'start_at' => $occasion['start_at'] ?? null,
                'end_at' => $occasion['end_at'] ?? null,
                'image' => $imagePath,
            ],
        );
    }

    private function createPoll(
        Request $request,
    ) {
        $pollData = $request->input('poll');
        $poll = Poll::create(
            [
                'question' => $request->input(
                    'content',
                ),
                'end_at' => now()->addDays(
                    (int) ($pollData['period'] ?? 3),
                ),
            ],
        );
        foreach ($pollData['options'] ?? [] as $option) {
            if (! empty($option)) {
                $poll->options()->create(['option' => $option]);
            }
        }

        return $poll;
    }

    // !createDraft
    public function createDraft(
        Request $request,
    ) {
        try {
            $user = Auth::user();
            if (! $user) {
                return failureRes('المستخدم غير مسجل الدخول');
            }
            $imageUpload = $request->hasFile('image')
                ? $this->handleUpload($request, $user, 'posts', 'image', $request->file('image'))
                : $request->input('image_url');
            $pdfUpload = $request->hasFile('pdf')
                ? $this->handleUpload($request, $user, 'pdfs', 'pdf', $request->file('pdf'))
                : $request->input('pdf_url');
            $videoUpload = $request->hasFile('video')
                ? $this->handleUpload($request, $user, 'videos', 'video', $request->file('video'))
                : $request->input('video_url');
            $imagePath = $imageUpload['file_url'] ?? null;
            $pdfPath = $pdfUpload['file_url'] ?? null;
            $videoPath = $videoUpload['file_url'] ?? null;
            $thumbnail_url = $videoUpload['thumbnail_url'] ?? null;
            $draft = Draft::updateOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'content' => $request->input(
                        'content',
                    ),
                    'image' => $imagePath,
                    'video' => $videoPath,
                    'thumbnail_url' => $thumbnail_url,
                    'pdf' => $pdfPath,
                ]
            );

            return successRes(
                new DraftResource(
                    $draft,
                ),
                200,
            );
        } catch (\Exception $e) {
            return failureRes(
                'حدث خطأ أثناء حفظ المسودة: '.$e->getMessage(),
            );
        }
    }

    // ! createSerReq
    public function createSerReq(
        Request $request,
    ) {
        $servReq = $request->input('serv_req');

        return ServiceRequest::create(
            [
                'title' => $servReq['title'] ?? '',
                'specialization_id' => $servReq['specialization_id'] ?? 1,
                'details' => $servReq['details'] ?? '',
            ],
        );
    }

    private function handleUpload(
        $request,
        $user,
        $folder,
        $field,
        $fallback = null,
    ) {
        return $request->hasFile($field)
            ? UploadFileHelper::uploadFile(
                $request,
                $user,
                $folder,
                $field,
            )
            : ($fallback ?? null
            );
    }

    public function delete(
        $id,
    ) {
        try {
            $post = Post::find(
                $id,
            );
            if (! $post) {
                return failureRes('المنشور غير موجود');
            }
            if ($post->user_id !== Auth::id()) {
                return failureRes('غير مصرح لك بحذف هذا المنشور');
            }
            if ($post->postable) {
                $post->postable->delete();
            }
            $post->delete();

            return successRes(
                null,
                204,
            );
        } catch (\Exception $e) {
            return failureRes(
                'حدث خطأ أثناء حذف المنشور: '.$e->getMessage(),
            );
        }
    }
}
