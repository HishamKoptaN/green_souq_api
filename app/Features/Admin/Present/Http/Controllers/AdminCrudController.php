<?php

namespace App\Features\Admin\Present\Http\Controllers;

use App\Features\Admin\Present\Resources\AdminResource;
use App\Features\Complaints\Data\Models\Complaint;
use App\Features\Jobs\Data\Models\Job;
use App\Features\Notifications\Data\Models\Notification;
use App\Features\Posts\Data\Models\Post;
use App\Features\Ratings\Data\Models\Rating;
use App\Features\Suggestions\Data\Models\Suggestion;
use App\Features\SupportTickets\Data\Models\SupportTicket;
use App\Features\Users\Data\Models\User;
use Illuminate\Http\Request;

class AdminCrudController
{
    private const MODELS = [
        'users' => User::class,
        'shops' => User::class,
        'posts' => Post::class,
        'ratings' => Rating::class,
        'tickets' => SupportTicket::class,
        'complaints' => Complaint::class,
        'suggestions' => Suggestion::class,
        'jobs' => Job::class,
        'notifications' => Notification::class,
    ];

    private const RULES = [
        'users' => ['name' => 'sometimes|string|max:255', 'phone' => 'sometimes|string|max:20', 'user_type' => 'sometimes|string|max:50'],
        'shops' => ['name' => 'sometimes|string|max:255', 'user_type' => 'sometimes|string|max:50'],
        'posts' => ['user_id' => 'required|exists:users,id', 'postable_type' => 'required|string', 'postable_id' => 'required|integer'],
        'ratings' => ['evaluator_id' => 'required|exists:users,id', 'rateable_type' => 'required|string', 'rateable_id' => 'required|integer', 'rating' => 'required|integer|min:1|max:5'],
        'tickets' => ['subject' => 'sometimes|string|max:255', 'type' => 'sometimes|string|max:50', 'description' => 'sometimes|string', 'status' => 'sometimes|in:open,closed'],
        'complaints' => ['title' => 'sometimes|string|max:255', 'description' => 'sometimes|string', 'status' => 'sometimes|string|max:50'],
        'suggestions' => ['title' => 'sometimes|string|max:255', 'description' => 'sometimes|string', 'status' => 'sometimes|string|max:50'],
        'jobs' => ['title' => 'sometimes|string|max:255', 'description' => 'sometimes|string', 'location' => 'sometimes|string|max:255', 'company_name' => 'sometimes|string|max:255'],
        'notifications' => ['title' => 'sometimes|string|max:255', 'body' => 'sometimes|string', 'type' => 'sometimes|string|max:50'],
    ];

    private const SEARCH_COLUMNS = [
        'users' => 'name', 'shops' => 'name', 'posts' => 'id', 'ratings' => 'id',
        'tickets' => 'subject', 'complaints' => 'title', 'suggestions' => 'title',
        'jobs' => 'title', 'notifications' => 'title',
    ];

    public function index(Request $request, string $resource)
    {
        $query = $this->model($resource)::query();

        if ($resource === 'shops') {
            $query->whereIn('user_type', ['farmer', 'shop']);
        }

        if ($request->filled('query')) {
            $query->where(self::SEARCH_COLUMNS[$resource], 'like', '%'.$request->query('query').'%');
        }

        return paginateRes($query->latest()->paginate(15), AdminResource::class);
    }

    public function show(string $resource, int $id)
    {
        return successRes(new AdminResource($this->model($resource)::findOrFail($id)));
    }

    public function store(Request $request, string $resource)
    {
        $data = $this->validate($request, $resource, false);

        if ($resource === 'tickets') {
            $data['type'] = $data['type'] ?? 'general';
            $data['user_id'] = $data['user_id'] ?? $request->user()->id;
        }
        if ($resource === 'complaints' || $resource === 'suggestions') {
            $data['user_id'] = $data['user_id'] ?? $request->user()->id;
        }

        return successRes(new AdminResource($this->model($resource)::create($data)), 201);
    }

    public function update(Request $request, string $resource, int $id)
    {
        $item = $this->model($resource)::findOrFail($id);
        $item->update($this->validate($request, $resource, true));

        return successRes(new AdminResource($item));
    }

    public function destroy(string $resource, int $id)
    {
        $this->model($resource)::findOrFail($id)->delete();

        return successRes(null);
    }

    private function model(string $resource): string
    {
        abort_unless(isset(self::MODELS[$resource]), 404);

        return self::MODELS[$resource];
    }

    private function validate(Request $request, string $resource, bool $partial): array
    {
        $rules = self::RULES[$resource];

        if ($partial) {
            $rules = array_map(fn ($rule) => preg_replace('/^required\|/', 'sometimes|', $rule), $rules);
        }

        return $request->validate($rules);
    }
}
