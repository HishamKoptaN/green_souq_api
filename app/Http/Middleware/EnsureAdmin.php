<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return failureRes('غير مصرح بالدخول إلى لوحة التحكم', 401, null, 'ADMIN_UNAUTHENTICATED');
        }

        if (! $user->isAdmin()) {
            return failureRes('صلاحيات المشرف مطلوبة للوصول إلى لوحة التحكم', 403, null, 'ADMIN_FORBIDDEN');
        }

        return $next($request);
    }
}
