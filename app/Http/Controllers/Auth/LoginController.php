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
    public function showLoginForm()
    {
        return view('auth.Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'id_staff' => 'required|string',
            'password' => 'required|string',
        ]);

        // Rate limiting
        $key = Str::lower($request->id_staff) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'id_staff' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        $credentials = $request->only('id_staff', 'password');
        $remember = $request->boolean('remember'); // ✅ boolean aman

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($key);
            $request->session()->regenerate();

            $user = Auth::user();

            // Optional: update last login
            if (isset($user->last_login_at)) {
                $user->last_login_at = now();
                $user->save();
            }

            session()->flash('success', 'Login berhasil, selamat datang ' . $user->name . '!');

            return $this->redirectBasedOnRole($user);
        }

        RateLimiter::hit($key, 60);

        return back()->withErrors([
            'id_staff' => 'ID Staff atau password salah.',
        ])->withInput($request->except('password'));
    }

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

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        session()->flash('success', 'Anda berhasil logout. Sampai jumpa!');

        return redirect('/');
    }
}
