<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // resources/views/auth/login.blade.php
    }

    public function login(Request $request)
    {
        // validasi input
        $request->validate([
            'id_staff' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('id_staff', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // set flash message
            session()->flash('success', 'Login berhasil, selamat datang ' . Auth::user()->name . '!');

            // redirect sesuai role
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'it') {
                return redirect()->route('it.dashboard');
            } else {
                return redirect()->route('user.dashboard');
            }
        }

        return back()->withErrors([
            'id_staff' => 'ID Staff atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // flash pesan logout sukses
        session()->flash('success', 'Anda berhasil logout. Sampai jumpa!');

        return redirect('/login');
    }
}
