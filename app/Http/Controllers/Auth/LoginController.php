<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.Login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $request->validate([
            'id_staff' => 'required|string',
            'password' => 'required|string',
        ]);

        // Rate limiting key generation
        $key = Str::lower($request->id_staff) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'id_staff' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        $credentials = $request->only('id_staff', 'password');
        $remember = $request->boolean('remember'); // ✅ safe boolean cast

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($key);
            $request->session()->regenerate();

            $user = Auth::user();

            // Optional: update last login timestamp
            // Note: Ensure the 'last_login_at' column exists on your User model's table.
            if (isset($user->last_login_at)) {
                $user->last_login_at = now();
                $user->save();
            }

            session()->flash('success', 'Login successful, welcome back ' . $user->name . '!');

            return $this->redirectBasedOnRole($user);
        }

        // If authentication fails, hit the rate limiter and redirect back.
        RateLimiter::hit($key, 60);

        return back()->withErrors([
            'id_staff' => 'Staff ID or password is incorrect.',
        ])->withInput($request->except('password'));
    }

    /**
     * Determine where to redirect users based on their role.
     *
     * @param mixed $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectBasedOnRole($user)
    {
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'tim it':
            case 'it':
                return redirect()->route('it.dashboard');
            default:
                return redirect()->route('staff.dashboard');
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        session()->flash('success', 'You have successfully logged out. Goodbye!');

        return redirect('/');
    }
}
