<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (!$request->user()->isActive()) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been deactivated. Please contact support.']);
        }

        if (!empty($roles) && !in_array($request->user()->role?->name, $roles)) {
            abort(403, 'Unauthorized. You do not have access to this area.');
        }

        return $next($request);
    }
}
