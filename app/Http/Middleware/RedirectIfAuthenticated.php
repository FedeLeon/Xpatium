<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        if (auth()->check()) {
            $role = auth()->user()?->role;
            $dashboardRoute = match ($role) {
                User::ROLE_ADMIN => 'admin.dashboard',
                User::ROLE_TEACHER => 'teacher.dashboard',
                User::ROLE_STUDENT => 'student.dashboard',
                default => 'home',
            };

            return redirect()->route($dashboardRoute);
        }

        return $next($request);
    }
}
