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
        ], [
            'id_staff.required' => 'Please enter your username.',
            'password.required' => 'Please enter your password.',
        ]);

        // Rate limiting key generation
        $key = Str::lower($request->id_staff) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);
            
            throw ValidationException::withMessages([
                'id_staff' => $seconds > 60 
                    ? "Too many failed login attempts. Please try again in {$minutes} minute(s) for security reasons."
                    : "Too many failed login attempts. Please try again in {$seconds} seconds for security reasons.",
            ]);
        }

        $credentials = $request->only('id_staff', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($key);
            $request->session()->regenerate();

            $user = Auth::user();

            // Optional: update last login timestamp
            if (isset($user->last_login_at)) {
                $user->last_login_at = now();
                $user->save();
            }

            // Enhanced welcome message with time-based greeting
            $greeting = $this->getTimeBasedGreeting();
            $welcomeMessage = "Welcome back, {$user->name}! {$greeting}";
            
            session()->flash('success', $welcomeMessage);

            return $this->redirectBasedOnRole($user);
        }

        // If authentication fails, hit the rate limiter and redirect back
        RateLimiter::hit($key, 60);
        
        $attemptsLeft = 5 - RateLimiter::attempts($key);

        return back()->withErrors([
            'id_staff' => $attemptsLeft > 0 
                ? "Invalid username or password. You have {$attemptsLeft} attempt(s) remaining."
                : 'Invalid username or password.',
        ])->withInput($request->except('password'));
    }

    /**
     * Get time-based greeting message.
     *
     * @return string
     */
    protected function getTimeBasedGreeting()
    {
        $hour = now()->hour;

        if ($hour >= 5 && $hour < 12) {
            return 'Have a productive morning!';
        } elseif ($hour >= 12 && $hour < 17) {
            return 'Have a great afternoon!';
        } elseif ($hour >= 17 && $hour < 21) {
            return 'Have a wonderful evening!';
        } else {
            return 'Working late? Take care!';
        }
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
        $userName = Auth::user()->name ?? 'User';
        
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        session()->flash('success', "Goodbye, {$userName}! You have been successfully logged out. See you again soon!");

        return redirect('/');
    }
}