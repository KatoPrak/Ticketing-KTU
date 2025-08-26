<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function admin()
    {
        return view('dashboard.admin');
    }

    public function it()
    {
        return view('dashboard.it');
    }

    public function user()
    {
        return view('dashboard.user');
    }
}
