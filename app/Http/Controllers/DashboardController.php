<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Category; // ✅ perbaikan namespace

class DashboardController extends Controller
{
    public function admin()
    {
        return view('admin.dashboard');
    }

    public function it()
    {
        $currentUser = Auth::user();

        // Base Query: Filter logic strictly for regional assignment
        $baseQuery = \App\Models\Ticket::query()->where(function ($query) use ($currentUser) {
            if ($currentUser->isAdmin()) {
                $query->where('assigned_to', $currentUser->id)
                      ->orWhereNull('assigned_to');
                return;
            }

            $query->where('assigned_to', $currentUser->id)
                  ->orWhere(function ($subQuery) use ($currentUser) {
                      $subQuery->whereNull('assigned_to')
                               ->where('region_id', $currentUser->region_id);
                  });
        });

        $activeTickets    = (clone $baseQuery)->whereIn('status', ['waiting','in_progress'])->count();
        $pendingTickets   = (clone $baseQuery)->where('status', 'pending')->count();
        $completedTickets = (clone $baseQuery)->whereIn('status', ['resolved','closed'])->count();
        $urgentTickets    = (clone $baseQuery)->where('priority', 'urgent')
                                              ->whereNotIn('status', ['resolved','closed'])
                                              ->count();

        // Recent tickets with location data
        $recentTickets = (clone $baseQuery)->with(['user.location', 'category', 'department', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('it.dashboard', compact(
            'activeTickets',
            'pendingTickets',
            'completedTickets',
            'urgentTickets',
            'recentTickets'
        ));
    }

    public function staff()
    {
        $categories = \App\Models\Category::all();
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // 📰 Fetch 3 latest news (Global OR User's Location)
        $news = \App\Models\News::where(function($q) use ($user) {
                $q->whereNull('location_id')
                  ->orWhere('location_id', $user->location_id);
            })
            ->latest()
            ->take(3)
            ->get();

        return view('staff.dashboard', compact('categories', 'news'));
    }


    public function index()
{
    $categories = \App\Models\Category::all();
    return view('staff.dashboard', compact('categories'));
}

}
