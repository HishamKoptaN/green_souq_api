<?php

namespace App\Features\Admin\Present\Http\Controllers;

use App\Features\Complaints\Data\Models\Complaint;
use App\Features\Jobs\Data\Models\Job;
use App\Features\Notifications\Data\Models\Notification;
use App\Features\Posts\Data\Models\Post;
use App\Features\Ratings\Data\Models\Rating;
use App\Features\Suggestions\Data\Models\Suggestion;
use App\Features\SupportTickets\Data\Models\SupportTicket;
use App\Features\Users\Data\Models\User;

class AdminStatsController
{
    public function index()
    {
        return successRes([
            'users' => User::count(),
            'shops' => User::whereIn('user_type', ['shop', 'farmer'])->count(),
            'farms' => User::where('user_type', 'farmer')->count(),
            'salesEngineers' => User::where('user_type', 'salesEngineer')->count(),
            'posts' => Post::count(),
            'openTickets' => SupportTicket::where('status', 'open')->count(),
            'complaints' => Complaint::count(),
            'suggestions' => Suggestion::count(),
            'jobs' => Job::count(),
            'averageRating' => round((float) (Rating::avg('rating') ?? 0), 1),
            'unreadNotifications' => Notification::where('is_read', false)->count(),
        ]);
    }

    public function chart()
    {
        $counts = Post::where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->get()
            ->groupBy(fn ($post) => $post->created_at->toDateString())
            ->map->count();

        $days = collect(range(0, 6))->map(function ($offset) use ($counts) {
            $date = now()->subDays($offset)->toDateString();

            return ['date' => $date, 'count' => $counts[$date] ?? 0];
        });

        return successRes($days->values()->reverse()->values());
    }

    public function activity()
    {
        $items = collect()
            ->concat(Complaint::latest()->limit(5)->get()->map(fn ($m) => $this->item('complaint', $m->title, $m->created_at)))
            ->concat(Suggestion::latest()->limit(5)->get()->map(fn ($m) => $this->item('suggestion', $m->title, $m->created_at)))
            ->concat(SupportTicket::latest()->limit(5)->get()->map(fn ($m) => $this->item('ticket', $m->subject, $m->created_at)))
            ->concat(Job::latest()->limit(5)->get()->map(fn ($m) => $this->item('job', $m->title, $m->created_at)));

        return successRes($items->sortByDesc('created_at')->take(20)->values());
    }

    private function item(string $type, string $label, $createdAt): array
    {
        return ['type' => $type, 'label' => $label, 'created_at' => $createdAt];
    }
}
