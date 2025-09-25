<?php

namespace App\Http\Controllers\It;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Category;

class TicketController extends Controller
{
    /**
     * Menampilkan daftar tiket IT dengan filter.
     */
    public function index(Request $request)
    {
        $categories = Category::all();

        $tickets = Ticket::with(['category', 'user'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($q2) use ($search) {
                    $q2->where('description', 'like', "%{$search}%")
                       ->orWhere('ticket_id', 'like', "%{$search}%")
                       ->orWhere('id', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('it.index-ticket', compact('categories', 'tickets'));
    }

    /**
     * Menampilkan detail tiket untuk modal (partial).
     */
    public function show($id)
    {
        $ticket = Ticket::with(['category', 'user', 'attachments'])->findOrFail($id);

        // Pastikan ini partial tanpa layout utama
        return view('it.partials.ticket-detail', compact('ticket'));
    }

    /**
     * Update status atau prioritas tiket.
     */
    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $ticket->update($request->only(['status', 'priority']));

        // Bisa dikembalikan response json jika ingin ajax, tapi untuk form biasa:
        return back()->with('success', 'Tiket berhasil diperbarui.');
    }

    /**
     * Menampilkan riwayat tiket (status done / closed) dengan filter.
     */
    public function riwayat(Request $request)
    {
        $categories = Category::all();

        $tickets = Ticket::with(['category', 'user'])
            ->whereIn('status', ['done', 'closed'])
            ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($q2) use ($search) {
                    $q2->where('description', 'like', "%{$search}%")
                       ->orWhere('ticket_id', 'like', "%{$search}%")
                       ->orWhere('id', 'like', "%{$search}%");
                });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('it.riwayat-ticket', compact('categories', 'tickets'));
    }
}
