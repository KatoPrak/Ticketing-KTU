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
    $activeTickets    = \App\Models\Ticket::whereIn('status', ['waiting','in_progress'])->count();
    $pendingTickets   = \App\Models\Ticket::where('status', 'pending')->count();
    $completedTickets = \App\Models\Ticket::whereIn('status', ['resolved','closed'])->count();
    $urgentTickets    = \App\Models\Ticket::where('priority', 'urgent')
                                          ->whereNotIn('status', ['resolved','closed'])
                                          ->count();

    // ambil maksimal 3 tiket terbaru
    $recentTickets = \App\Models\Ticket::with(['user','category'])
        ->orderBy('created_at', 'desc')
        ->take(3)
        ->get();

    return view('it.IT', compact(
        'activeTickets',
        'pendingTickets',
        'completedTickets',
        'urgentTickets',
        'recentTickets'
    ));
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
