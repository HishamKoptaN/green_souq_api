<?php

namespace App\Features\Notifications\Present\Controllers;

use App\Http\Resources\Dash\NotificationResource;
use App\Models\System\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsApiController
{
    protected function get(Request $request)
    {
        try {
            $currentUser = Auth::guard('sanctum')->user();
            $query = Notification::query();
            if (! $currentUser) {
                return failureRes(
                    'User not authenticated',
                    401
                );
            }
            $query->where(function ($q) use ($currentUser) {
                $q->where('user_id', $currentUser->id)
                    ->orWhere('target_type', 'all')
                    ->orWhere(function ($subQuery) use ($currentUser) {
                        $subQuery->where('target_type', 'role')
                            ->whereIn('target_value', $currentUser->getRoleNames()->toArray());
                    })
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('target_type', 'topic')
                            ->whereIn('target_value', ['general', 'system_alerts', 'new_orders']);
                    });
            });
            if ($request->has('read') && $request->get('read') !== null) {
                $query->where('is_read', $request->get('read'));
            }
            if ($request->has('type') && ! empty($request->get('type'))) {
                $query->where('type', $request->get('type'));
            }
            if ($request->has('priority') && ! empty($request->get('priority'))) {
                $query->where('priority', $request->get('priority'));
            }
            if ($request->has('query') && ! empty($request->get('query'))) {
                $search = $request->get('query');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('body', 'LIKE', "%{$search}%")
                        ->orWhere('id', 'LIKE', "%{$search}%");
                });
            }
            $notifications = $query->orderBy('created_at', 'desc')->take(30)->get();
            foreach ($request->query() as $key => $value) {
                if (! empty($value)) {
                    $notifications->appends([$key => $value]);
                }
            }

            return successRes(NotificationResource::collection($notifications), 200);
        } catch (\Exception $e) {
            return failureRes(
                $e->getMessage(),
            );
        }
    }

    protected function markAsRead(Request $request, $id)
    {
        try {
            $notification = Notification::find($id);
            if (! $notification) {
                return failureRes(
                    'Notification not found',
                    404
                );
            }
            $notification->markAsRead();

            return successRes(
                'Notification marked as read'
            );
        } catch (\Exception $e) {
            return failureRes(
                $e->getMessage(),
            );
        }
    }

    protected function markAllAsRead(Request $request)
    {
        try {
            $currentUser = auth()->user();
            if (! $currentUser) {
                return failureRes(
                    'User not authenticated',
                    401
                );
            }
            $query = Notification::query();
            $query->where(function ($q) use ($currentUser) {
                $q->where('user_id', $currentUser->id)
                    ->orWhere('target_type', 'all')
                    ->orWhere(function ($subQuery) use ($currentUser) {
                        $subQuery->where('target_type', 'role')
                            ->whereIn('target_value', $currentUser->getRoleNames()->toArray());
                    })
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('target_type', 'topic')
                            ->whereIn('target_value', ['general', 'system_alerts', 'new_orders']);
                    });
            });
            $query->where('is_read', false)->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

            return successRes(
                'All notifications marked as read'
            );
        } catch (\Exception $e) {
            return failureRes(
                $e->getMessage(),
            );
        }
    }

    protected function delete(Request $request, $id)
    {
        try {
            $notification = Notification::find($id);

            if (! $notification) {
                return failureRes(
                    'Notification not found',
                    404
                );
            }
            $notification->delete();

            return successRes(
                'Notification deleted successfully'
            );
        } catch (\Exception $e) {
            return failureRes(
                $e->getMessage(),
            );
        }
    }
}
