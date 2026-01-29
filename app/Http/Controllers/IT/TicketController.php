<?php

namespace App\Http\Controllers\It;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TicketController extends Controller
{
    // ============================================================
    // 📋 INDEX — Daftar tiket aktif
    // ============================================================
    public function index(Request $request)
    {
        $categories = Category::all();

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
    // ✅ FIXED: NULL-safe untuk updated_at dan resolved_at
    // ============================================================
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

        return response()->json([
            'id' => $ticket->id,
            'ticket_id' => $ticket->ticket_id,
            'description' => $ticket->description,
            'priority' => $ticket->priority,
            'status' => $ticket->status,
            'attachments' => $attachments,
            'resolution_notes' => $ticket->resolution_notes ?? '',
            
            // ✅ DATES - NULL-safe menggunakan accessor dari Model
            'report_date' => $ticket->created_at_formatted,
            'created_at_formatted' => $ticket->created_at_formatted,
            
            // ✅ Response Date = updated_at (bisa NULL untuk waiting)
            'response_date' => $ticket->updated_at_formatted,
            'response_at_formatted' => $ticket->updated_at_formatted,
            
            // ✅ Resolved Date = resolved_at (bisa NULL)
            'resolved_date' => $ticket->resolved_at 
                ? $ticket->resolved_at->format('d M Y H:i')
                : null,
            'resolved_at_formatted' => $ticket->resolved_at_formatted,
            
            // Relations
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
            'priority' => 'nullable|in:low,medium,high,urgent,critical',
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
    // ✅ FIXED: NULL-safe untuk response
    // ============================================================
    public function updateField(Request $request, Ticket $ticket)
    {
        $validator = Validator::make($request->all(), [
            'field' => 'required|in:status,priority',
            'value' => 'required|string',
            'resolution_notes' => 'nullable|string|max:2000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $field = $validated['field'];
        $value = $validated['value'];

        if ($field === 'status') {
            $resolutionNotes = $validated['resolution_notes'] ?? null;
            
            // Update status
            $ticket->status = $value;
            
            // Simpan resolution_notes untuk pending, resolved, dan closed
            if (in_array($value, ['pending', 'resolved', 'closed']) && $resolutionNotes) {
                $ticket->resolution_notes = $resolutionNotes;
            }
            
            $ticket->save();
            
        } else {
            // For priority updates
            $ticket->{$field} = $value;
            $ticket->save();
        }

        // Muat relasi terbaru dan refresh dari database
        $ticket->load('category', 'user', 'department');
        $ticket->refresh();

        // ✅ Response dengan NULL-safe menggunakan accessor dari Model
        return response()->json([
            'success' => true,
            'message' => ucfirst($field) . ' updated successfully.',
            'ticket' => [
                'id' => $ticket->id,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'response_date' => $ticket->updated_at_formatted,
                'response_at_formatted' => $ticket->updated_at_formatted,
                'resolved_date' => $ticket->resolved_at 
                    ? $ticket->resolved_at->format('d M Y H:i')
                    : null,
                'resolved_at_formatted' => $ticket->resolved_at_formatted,
            ]
        ]);
    }

    // ============================================================
    // 🗂️ RIWAYAT — Tiket yang sudah closed dengan filter lengkap
    // ============================================================
    public function riwayat(Request $request)
    {
        $categories = Category::all();

        $tickets = Ticket::with(['category', 'user', 'department'])
            ->where('status', 'closed')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function($q2) use ($search) {
                    $q2->where('description', 'like', "%{$search}%")
                       ->orWhere('ticket_id', 'like', "%{$search}%")
                       ->orWhere('id', 'like', "%{$search}%")
                       ->orWhereHas('user', function($q3) use ($search) {
                           $q3->where('name', 'like', "%{$search}%");
                       });
                });
            })
            ->when($request->filled('start_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->start_date);
            })
            ->when($request->filled('end_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->end_date);
            })
            ->when($request->filled('category'), function ($q) use ($request) {
                $q->where('category_id', $request->category);
            })
            ->when($request->filled('priority'), function ($q) use ($request) {
                $q->where('priority', $request->priority);
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderBy('resolved_at', 'desc')
            ->paginate(10)
            ->appends($request->query());

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
    }
}