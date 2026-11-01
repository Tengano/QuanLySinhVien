<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KiemTraVaiTro
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_if(
            ! $user || ! $user->vai_tro || ! in_array($user->vai_tro->value, $roles, true),
            403,
            'Bạn không có quyền truy cập chức năng này.'
        );

        return $next($request);
    }
}
