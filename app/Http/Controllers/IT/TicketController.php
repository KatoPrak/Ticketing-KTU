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
    $categories = Category::all();

    $tickets = Ticket::with(['category', 'user'])
        // 🚀 hanya ambil tiket yang masih aktif
        ->whereNotIn('status', ['closed'])
        ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
        ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->category_id))
        ->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->search;
            $q->where(fn($q2) => $q2
                ->where('description', 'like', "%{$search}%")
                ->orWhere('ticket_id', 'like', "%{$search}%")
                ->orWhere('id', 'like', "%{$search}%")
            );
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    $recentTickets = Ticket::with(['category', 'user'])
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();

    return view('it.index-ticket', compact('categories', 'tickets', 'recentTickets'));
}


  public function show($id)
{
    $ticket = Ticket::with(['user', 'category'])->findOrFail($id);

    return response()->json([
        'id' => $ticket->id,
        'ticket_id' => $ticket->ticket_id,
        'description' => $ticket->description,
        'priority' => $ticket->priority,
        'status' => $ticket->status,
        'attachments' => $ticket->attachments ?? [],
        'created_at' => $ticket->created_at->format('d-m-Y H:i'),
        'user' => [
            'name' => $ticket->user->name ?? 'Unknown',
            'department' => $ticket->user->department ?? '-',
        ],
        'category' => [
            'name' => $ticket->category->name ?? '-',
        ],
    ]);
}



    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status'   => 'nullable|in:waiting,in_progress,pending,resolved,closed',
            'priority' => 'nullable|in:low,medium,high,urgent',
        ]);

        $ticket = Ticket::findOrFail($id);
        $ticket->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tiket berhasil diperbarui.',
                'ticket'  => $ticket
            ]);
        }

        return back()->with('success', 'Tiket berhasil diperbarui.');
    }

    /**
     * ✅ Update field tunggal via AJAX (status / priority)
     */
    public function updateField(Request $request, $id)
{
    try {
        $request->validate([
            'field' => 'required|in:status,priority',
            'value' => 'required|string'
        ]);

        $ticket = Ticket::findOrFail($id);

        // cek value valid sesuai enum
        if ($request->field === 'status' && !in_array($request->value, ['waiting','in_progress','pending','resolved','closed'])) {
            return response()->json(['success' => false, 'message' => 'Invalid status value.'], 422);
        }
        if ($request->field === 'priority' && !in_array($request->value, ['low','medium','high','urgent'])) {
            return response()->json(['success' => false, 'message' => 'Invalid priority value.'], 422);
        }

        $ticket->{$request->field} = $request->value;
        $ticket->save();

        return response()->json([
            'success' => true,
            'message' => ucfirst($request->field).' updated successfully.',
            'ticket' => $ticket->load('category','user') // kirim data lengkap untuk JS
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}


    public function riwayat(Request $request)
{
    $categories = Category::all();

    $tickets = Ticket::with(['category', 'user'])
    ->whereIn('status', ['closed'])
    ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
    ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->category_id))
    ->when($request->filled('search'), function ($q) use ($request) {
        $search = $request->search;
        $q->where(fn($q2) => $q2
            ->where('description', 'like', "%{$search}%")
            ->orWhere('ticket_id', 'like', "%{$search}%")
            ->orWhere('id', 'like', "%{$search}%")
        );
    })
    ->orderBy('updated_at', 'desc')
    ->paginate(10);


    $recentTickets = Ticket::with(['category', 'user'])
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();

    return view('it.riwayat-ticket', compact('categories', 'tickets', 'recentTickets'));
}


    public function dashboard()
    {
        $activeTickets    = Ticket::whereIn('status', ['waiting','in_progress'])->count();
        $pendingTickets   = Ticket::where('status', 'pending')->count();
        $completedTickets = Ticket::where('status', 'resolved')->count();
        $urgentTickets    = Ticket::where('priority', 'urgent')->where('status', '!=', 'resolved')->count();

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

    public function store(Request $request)
{
    $validated = $request->validate([
        'category_id' => 'required|exists:categories,id',
        'description' => 'required|string|max:1000',
        'priority'    => 'nullable|in:low,medium,high,urgent',
        'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:2048',
    ]);

    // Bagian ini untuk generate Ticket ID (sudah oke)
    $lastTicket = Ticket::orderBy('id', 'desc')->first();
    $nextId = $lastTicket ? $lastTicket->id + 1 : 1;
    $ticketId = 'IT-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

    // Siapkan array untuk menampung path file
    $attachmentPaths = [];
    if ($request->hasFile('attachments')) {
        foreach ($request->file('attachments') as $file) {
            // Simpan file ke 'storage/app/public/tickets' dan kumpulkan path-nya
            $path = $file->store('tickets', 'public');
            $attachmentPaths[] = $path;
        }
    }

    // Buat tiket dan langsung masukkan array path ke kolom 'attachments'
    $ticket = Ticket::create([
        'ticket_id'   => $ticketId,
        'user_id'     => auth()->id(),
        'category_id' => $validated['category_id'],
        'description' => $validated['description'],
        'priority'    => $validated['priority'] ?? 'medium',
        'status'      => 'waiting',
        'attachments' => $attachmentPaths, // Simpan array path ke kolom JSON
    ]);

    // BLOK KODE YANG DUPLIKAT SUDAH DIHAPUS DARI SINI

    return back()->with('success', 'Tiket berhasil dibuat dengan ID: ' . $ticketId);
}
}
