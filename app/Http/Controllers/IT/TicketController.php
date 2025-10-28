<?php

namespace App\Http\Controllers\It;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    // ============================================================
    // 📋 INDEX — Daftar tiket aktif
    // ============================================================
    public function index(Request $request)
    {
        $categories = Category::all();

        // FIX: Tambahkan relasi 'department' untuk ditampilkan di tabel
        $tickets = Ticket::with(['category', 'user', 'department'])
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

        return view('it.index-ticket', compact('categories', 'tickets'));
    }


    // ============================================================
    // 🎫 SHOW — Detail tiket (dipakai di modal AJAX)
    // ============================================================
// Ganti method show() Anda yang lama dengan yang ini

public function show(Ticket $ticket)
{
    // Muat semua relasi yang dibutuhkan
    $ticket->load(['user', 'category', 'department']);

    // Logika untuk memastikan attachments selalu berupa array
    $attachments = $ticket->attachments;
    if (is_string($attachments)) {
        $decoded = json_decode($attachments, true);
        $attachments = is_array($decoded) ? $decoded : [];
    } elseif (!is_array($attachments)) {
        $attachments = [];
    }

    // FIX: Gunakan optional() untuk mencegah error jika relasi kosong
    return response()->json([
        'id' => $ticket->id,
        'ticket_id' => $ticket->ticket_id,
        'description' => $ticket->description,
        'priority' => $ticket->priority,
        'status' => $ticket->status,
        'attachments' => $attachments,
        'resolution_notes' => $ticket->resolution_notes ?? '',
        'created_at' => optional($ticket->created_at)->format('d-m-Y H:i'),
        'user' => [
            'name' => optional($ticket->user)->name ?? 'User Deleted',
        ],
        'category' => [
            'name' => optional($ticket->category)->name ?? 'No Category',
        ],
        'department' => [
            'name' => optional($ticket->department)->name ?? 'No Department',
        ],
    ]);
}

    // ============================================================
    // 🧭 UPDATE (umum)
    // ============================================================
public function update(Request $request, Ticket $ticket)
{
    $validated = $request->validate([
        'status'   => 'nullable|in:waiting,in_progress,pending,resolved,closed',
        'priority' => 'nullable|in:low,medium,high,urgent',
    ]);
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

    // ============================================================
    // ⚙️ UPDATE FIELD (via AJAX)
    // ============================================================
public function updateField(Request $request, Ticket $ticket)
{
    // Gunakan Validator facade untuk kontrol penuh
    $validator = Validator::make($request->all(), [
        'field' => 'required|in:status,priority',
        'value' => 'required|string',
        'resolution_notes' => 'nullable|string|max:2000'
    ]);

    // Jika validasi gagal, kirim response error
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation error.',
            'errors' => $validator->errors()
        ], 422);
    }

    // Ambil data yang sudah tervalidasi
    $validated = $validator->validated();
    
    $field = $validated['field'];
    $value = $validated['value'];

    $ticket->{$field} = $value;

    // Cek dan simpan resolution_notes jika ada
    if (array_key_exists('resolution_notes', $validated) && $field === 'status' && in_array($value, ['pending', 'closed', 'resolved'])) {
        $ticket->resolution_notes = $validated['resolution_notes'];
    }
    
    $ticket->save();

    // Muat relasi terbaru sebelum mengirim response
    $ticket->load('category','user', 'department');

    return response()->json([
        'success' => true,
        'message' => ucfirst($field).' updated successfully.',
        'ticket' => $ticket
    ]);
}

     // ============================================================
    // 🗂️ RIWAYAT — Tiket yang sudah closed
    // ============================================================
    public function riwayat(Request $request)
    {
        $categories = Category::all();

        // FIX: Tambahkan relasi 'department'
        $tickets = Ticket::with(['category', 'user', 'department'])
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
        
        return view('it.riwayat-ticket', compact('categories', 'tickets'));
    }


    // ============================================================
    // 📊 DASHBOARD IT
    // ============================================================
    public function dashboard()
    {
        $activeTickets    = Ticket::whereIn('status', ['waiting','in_progress'])->count();
        $pendingTickets   = Ticket::where('status', 'pending')->count();
        $completedTickets = Ticket::where('status', 'resolved')->count();
        $urgentTickets    = Ticket::where('priority', 'urgent')->where('status', '!=', 'resolved')->count();

$recentTickets = Ticket::with(['category', 'user', 'department'])
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
    }}

