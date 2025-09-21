<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RememberMeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is already authenticated
        if (Auth::check()) {
            return $next($request);
        }
        
        // Check for remember me cookie
        $rememberCookie = $request->cookie(Auth::getRecallerName());
        
        if ($rememberCookie) {
            $segments = explode('|', $rememberCookie);
            
            if (count($segments) === 2) {
                [$userId, $token] = $segments;
                
                $user = User::find($userId);
                
                if ($user && $user->remember_token && hash_equals($user->remember_token, hash('sha256', $token))) {
                    // Auto login the user
                    Auth::login($user);
                    
                    // Regenerate session
                    $request->session()->regenerate();
                    
                    // Update last login
                    $user->updateLastLogin();
                    
                    // Log the auto login (optional)
                    \Log::info("User {$user->id_staff} auto-logged in via remember token", [
                        'user_id' => $user->id,
                        'ip' => $request->ip()
                    ]);
                } else {
                    // Invalid token, remove the cookie
                    if ($user) {
                        $user->clearRememberToken();
                    }
                }
            }
        }
        
        return $next($request);
    }
}