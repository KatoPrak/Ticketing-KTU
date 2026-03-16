<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                $role = strtolower($user->role ?? '');
                
                $dashboardRoutes = [
                    'admin'      => 'admin.dashboard',
                    'tim it'     => 'it.dashboard',
                    'user'       => 'staff.dashboard',
                ];

                if (isset($dashboardRoutes[$role]) && \Illuminate\Support\Facades\Route::has($dashboardRoutes[$role])) {
                    return redirect()->route($dashboardRoutes[$role]);
                }
                
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
