<?php

namespace App\Http\Controllers\It;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Category;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        // ambil kategori (selalu koleksi, agar tidak undefined)
        $categories = Category::all() ?? collect();

        $query = Ticket::with('category', 'user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(10);

        // memastikan kita mengirim 'categories' dan 'tickets'
        return view('it.riwayat-ticket', compact('categories', 'tickets'));
    }

    public function riwayat()
    {
        $tickets = Ticket::with('category', 'user')
            ->whereIn('status', ['selesai', 'ditutup'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('it.riwayat-ticket', compact('tickets'));
    }
}
