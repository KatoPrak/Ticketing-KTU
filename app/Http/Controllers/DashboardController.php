<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Category; // ✅ perbaikan namespace

class DashboardController extends Controller
{
    public function admin()
    {
        return view('admin.Admin');
    }

    public function it()
    {
        return view('it.IT');
    }

    public function staff()
    {
        $categories = Category::all(); // ✅ ambil data kategori
        return view('staff.staff', compact('categories')); // ✅ kirim ke blade
    }

    public function index()
{
    $categories = \App\Models\Category::all();
    return view('staff.staff', compact('categories'));
}

}
