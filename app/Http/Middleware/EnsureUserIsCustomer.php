<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('web')->check()) {
            return redirect()->route('login');
        }

        $user = Auth::guard('web')->user();

        abort_if(!$user || !$user->is_active, Response::HTTP_FORBIDDEN);

        return $next($request);
    }
}
