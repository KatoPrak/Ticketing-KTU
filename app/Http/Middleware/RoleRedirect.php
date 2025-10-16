<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleRedirect
{
    /**
     * Handle an incoming request.
     * 
     * Middleware ini digunakan untuk:
     * 1. Mencegah user mengakses halaman role lain
     * 2. Redirect ke dashboard yang sesuai jika mencoba akses halaman yang tidak sesuai role
     */
    public function handle(Request $request, Closure $next, ?string $allowedRole = null): Response
    {
        // Jika belum login, biarkan auth middleware yang handle
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $userRole = strtolower($user->role ?? '');

        // Jika tidak ada role yang diexpect, lanjutkan request
        if (!$allowedRole) {
            return $next($request);
        }

        $allowedRole = strtolower($allowedRole);

        // Normalisasi: 'user' dan 'staff' dianggap sama
        if ($userRole === 'user') {
            $userRole = 'staff';
        }
        if ($allowedRole === 'user') {
            $allowedRole = 'staff';
        }

        // Jika role sesuai, lanjutkan request
        if ($userRole === $allowedRole) {
            return $next($request);
        }

        // Jika role tidak sesuai, redirect ke dashboard yang tepat
        return $this->redirectToProperDashboard($userRole);
    }

    /**
     * Redirect user ke dashboard yang sesuai dengan role mereka
     */
    private function redirectToProperDashboard(string $role): Response
    {
        $dashboardRoutes = [
            'admin'      => 'admin.dashboard',
            'it'         => 'it.dashboard',
            'staff'      => 'staff.dashboard',
            'user'       => 'staff.dashboard', // alias untuk staff
        ];

        $routeName = $dashboardRoutes[$role] ?? null;

        // Cek apakah route ada
        if ($routeName && \Route::has($routeName)) {
            return redirect()
                ->route($routeName)
                ->with('warning', 'You do not have permission to access that page.');
        }

        // Jika route tidak ditemukan, cek alternatif
        // Coba redirect ke home atau logout
        if (\Route::has('home')) {
            return redirect()->route('home')->with('error', 'Dashboard not configured for your role.');
        }

        // Last resort: logout dan redirect ke login
        Auth::logout();
        return redirect()
            ->route('login')
            ->with('error', 'Your account role is not properly configured. Please contact administrator.');
    }
}