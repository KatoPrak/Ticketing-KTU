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
     * 3. Mendukung multiple roles dan single role
     */
    public function handle(Request $request, Closure $next, ...$allowedRoles): Response
    {
        // Jika belum login, redirect ke login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $userRole = strtolower($user->role ?? '');

        // Jika tidak ada role yang diexpect, lanjutkan request
        if (empty($allowedRoles)) {
            return $next($request);
        }

        // Normalisasi roles: 'user' dan 'staff' dianggap sama
        $normalizedUserRole = $this->normalizeRole($userRole);
        $normalizedAllowedRoles = array_map([$this, 'normalizeRole'], $allowedRoles);

        // Jika role sesuai dengan salah satu yang diizinkan, lanjutkan request
        if (in_array($normalizedUserRole, $normalizedAllowedRoles)) {
            return $next($request);
        }

        // Jika role tidak sesuai, redirect ke dashboard yang tepat
        return $this->redirectToProperDashboard($normalizedUserRole);
    }

    /**
     * Normalize role names for consistency
     */
    private function normalizeRole(string $role): string
    {
        $role = strtolower(trim($role));
        
        // Normalisasi: 'user' dan 'staff' dianggap sama
        if ($role === 'user') {
            return 'staff';
        }
        
        // Handle "tim it" role
        if ($role === 'tim it' || $role === 'it') {
            return 'tim it';
        }
        
        return $role;
    }

    /**
     * Redirect user ke dashboard yang sesuai dengan role mereka
     */
    private function redirectToProperDashboard(string $role): Response
    {
        $dashboardRoutes = [
            'admin'      => 'admin.dashboard',
            'it'         => 'it.dashboard',
            'tim it'     => 'it.dashboard',
            'staff'      => 'staff.dashboard',
            'user'       => 'staff.dashboard', // alias untuk staff
        ];

        $routeName = $dashboardRoutes[$role] ?? null;

        // Cek apakah route ada
        if ($routeName && \Route::has($routeName)) {
            return redirect()
                ->route($routeName)
                ->with('warning', 'Anda tidak memiliki izin untuk mengakses halaman tersebut.');
        }

         // Fallback routes
        $fallbackRoutes = [
            'admin' => 'admin.dashboard',
            'tim it' => 'it.dashboard',
            'it' => 'it.dashboard',
            'staff' => 'staff.dashboard',
        ];

        $fallbackRoute = $fallbackRoutes[$role] ?? 'home';

        if (\Route::has($fallbackRoute)) {
            return redirect()
                ->route($fallbackRoute)
                ->with('warning', 'Anda tidak memiliki izin untuk mengakses halaman tersebut.');
        }

        // Jika route tidak ditemukan, coba redirect ke home
        if (\Route::has('home')) {
            return redirect()
                ->route('home')
                ->with('error', 'Dashboard tidak dikonfigurasi untuk role Anda.');
        }

        // Last resort: logout dan redirect ke login
        Auth::logout();
        return redirect()
            ->route('login')
            ->with('error', 'Role akun Anda tidak dikonfigurasi dengan benar. Silakan hubungi administrator.');
    }
}