<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_if(
            ! $user || ! $user->role || ! in_array($user->role->value, $roles, true),
            403,
            'Bạn không có quyền truy cập chức năng này.'
        );

        return $next($request);
    }
}
