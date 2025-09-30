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

        // tiket terbaru untuk sidebar/recent activity
        $recentTickets = Ticket::with(['category', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('it.index-ticket', compact('categories', 'tickets', 'recentTickets'));
    }

    /**
     * Update status atau prioritas tiket.
     */
    public function update(Request $request, $id)
    {
        // ✅ enum di DB: waiting, in_progress, pending, resolve
        $validated = $request->validate([
            'status'   => 'nullable|in:waiting,in_progress,pending,resolve',
            'priority' => 'nullable|in:low,medium,high,urgent',
        ]);

        $ticket = Ticket::findOrFail($id);
        $ticket->update($validated);

        return back()->with('success', 'Tiket berhasil diperbarui.');
    }

    /**
     * Menampilkan riwayat tiket (status resolve).
     */
    public function riwayat(Request $request)
    {
        $categories = Category::all();

        $tickets = Ticket::with(['category', 'user'])
            ->where('status', 'resolve') // ✅ sesuai enum DB
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

        // ✅ tambahkan recentTickets biar bisa dipakai juga
        $recentTickets = Ticket::with(['category', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('it.riwayat-ticket', compact('categories', 'tickets', 'recentTickets'));
    }

    /**
     * Menampilkan halaman dashboard IT.
     */
    public function dashboard()
    {
        $activeTickets    = Ticket::whereIn('status', ['waiting','in_progress'])->count();
        $pendingTickets   = Ticket::where('status', 'pending')->count();
        $completedTickets = Ticket::where('status', 'resolve')->count();
        $urgentTickets    = Ticket::where('priority', 'urgent')
                                ->where('status', '!=', 'resolve')
                                ->count();

        $recentTickets = Ticket::with(['category', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('it.IT', compact(
            'activeTickets',
            'pendingTickets',
            'completedTickets',
            'urgentTickets',
            'recentTickets'
        ));
    }

    /**
     * Simpan tiket baru dari IT.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'description'   => 'required|string|max:1000',
            'priority'      => 'nullable|in:low,medium,high,urgent',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:2048',
        ]);

        $lastTicket = Ticket::orderBy('id', 'desc')->first();
        $nextId = $lastTicket ? $lastTicket->id + 1 : 1;
        $ticketId = 'IT-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

        $ticket = Ticket::create([
            'ticket_id'   => $ticketId,
            'user_id'     => auth()->id(),
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'priority'    => $validated['priority'] ?? 'medium',
            'status'      => 'waiting',
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets', 'public');
                $ticket->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        return back()->with('success', 'Tiket berhasil dibuat dengan ID: ' . $ticketId);
    }
}
