<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleRedirect
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $role = Auth::user()->role;

            switch ($role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'it':
                    return redirect()->route('it.dashboard');
                case 'user':
                    return redirect()->route('user.dashboard');
            }
        }

        return $next($request);
    }
}
