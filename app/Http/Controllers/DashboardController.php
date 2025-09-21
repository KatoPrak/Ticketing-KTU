<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Category; // ✅ perbaikan namespace

class DashboardController extends Controller
{
    public function admin()
    {
        return view('Dashboard.Admin');
    }

    public function it()
    {
        return view('Dashboard.IT');
    }

    public function staff()
    {
        $categories = Category::all(); // ✅ ambil data kategori
        return view('Dashboard.staff', compact('categories')); // ✅ kirim ke blade
    }

    public function index()
{
    $categories = \App\Models\Category::all();
    return view('Dashboard.staff', compact('categories'));
}

}
