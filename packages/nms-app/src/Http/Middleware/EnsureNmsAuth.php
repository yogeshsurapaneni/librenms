<?php

namespace NmsApp\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureNmsAuth
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()) {
            return $next($request);
        }
        if ($request->is('nms/api/*') || $request->is('nms/auth/*')) {
            return response()->json(['status' => 'error', 'message' => 'unauthenticated'], 401);
        }

        return redirect('/login');
    }
}
