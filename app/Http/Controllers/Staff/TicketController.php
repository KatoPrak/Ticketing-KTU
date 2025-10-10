<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Category;
use App\Models\News;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketCreatedMail;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    /**
     * Halaman dashboard staff.
     * Menampilkan berita dan tiket terbaru.
     */
    public function dashboard()
    {
        // Ambil 3 berita terbaru (bisa diubah sesuai kebutuhan)
        $news = News::orderBy('created_at', 'desc')->take(3)->get();

        return view('staff.staff', compact('news'));
    }

    /**
     * Simpan tiket baru ke database dan buat ID kustom.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'description'   => 'required|string',
            'attachments.*' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,heif',
        ]);

        // 🚀 Simpan file lampiran
        $filePaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filePaths[] = $file->store('tickets', 'public');
            }
        }

        // 🚀 Buat tiket baru
        $ticket = Ticket::create([
            'user_id'     => Auth::id(),
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'attachments' => json_encode($filePaths),
            'status'      => 'waiting',
            'priority'    => 'low',
        ]);

        // 🚀 Format ticket_id (misal: T-KTU-001)
        $formattedId = 'T-KTU-' . str_pad($ticket->id, 3, '0', STR_PAD_LEFT);
        $ticket->ticket_id = $formattedId;
        $ticket->save();

        // 🚀 Load relasi kategori & user untuk email
        $ticket->load(['category', 'user']);
        $ticket->attachments = $filePaths;

        // 📧 KIRIM EMAIL NOTIFIKASI KE TIM IT
        try {
            $itTeamEmail = env('IT_TEAM_EMAIL', 'irvanronaldi2@gmail.com');
            
            Mail::to($itTeamEmail)->send(new TicketCreatedMail($ticket));
            
            \Log::info('Email notifikasi tiket berhasil dikirim', [
                'ticket_id' => $ticket->ticket_id,
                'to' => $itTeamEmail,
                'user' => Auth::user()->name
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim email notifikasi tiket baru', [
                'ticket_id' => $ticket->ticket_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        // 🚀 Return JSON
        return response()->json([
            'success' => true,
            'message' => 'Tiket berhasil dibuat dengan ID: ' . $ticket->ticket_id,
            'ticket'  => [
                'id'          => $ticket->id,
                'ticket_id'   => $ticket->ticket_id,
                'category'    => $ticket->category ? [
                    'id'   => $ticket->category->id,
                    'name' => $ticket->category->name,
                ] : null,
                'description' => $ticket->description,
                'status'      => $ticket->status,
                'priority'    => $ticket->priority,
                'created_at'  => $ticket->created_at->format('d M Y H:i'),
                'attachments' => $filePaths,
            ]
        ], 201);
    }

    /**
     * Tampilkan daftar tiket aktif & riwayat.
     */
    public function index(Request $request)
    {
        $categories = Category::all();

        // ==================== QUERY TIKET AKTIF ====================
        $activeQuery = Ticket::with('category')
            ->where('user_id', Auth::id())
            ->whereNotIn('status', ['resolved', 'closed']); // exclude yg selesai

        if ($request->status) {
            $activeQuery->where('status', $request->status);
        }

        if ($request->category_id) {
            $activeQuery->where('category_id', $request->category_id);
        }

        if ($request->search) {
            $activeQuery->where(function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                    ->orWhere('id', 'like', '%' . $request->search . '%')
                    ->orWhere('ticket_id', 'like', '%' . $request->search . '%');
            });
        }

        $tickets = $activeQuery->orderBy('created_at', 'desc')->paginate(5);

        $tickets->getCollection()->transform(function ($ticket) {
            $ticket->attachments = json_decode($ticket->attachments, true) ?? [];
            return $ticket;
        });

        // ==================== QUERY TIKET RIWAYAT ====================
        $historyTickets = Ticket::with('category')
            ->where('user_id', Auth::id())
            ->whereIn('status', ['resolved', 'closed'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($ticket) {
                $ticket->attachments = json_decode($ticket->attachments, true) ?? [];
                return $ticket;
            });

        // ==================== RESPONSE AJAX ====================
        if ($request->ajax()) {
            return response()->json([
                'tickets' => $tickets,
                'historyTickets' => $historyTickets,
            ], 200);
        }

        return view('staff.list-tiket', compact('categories', 'tickets', 'historyTickets'));
    }

    /**
     * Ambil tiket terbaru untuk dashboard staff.
     */
    public function fetchDashboardTickets()
    {
        $tickets = Ticket::where('user_id', auth()->id())
            ->latest()
            ->take(3)
            ->get();

        return response()->json($tickets);
    }

    /**
     * Ambil detail news untuk modal detail.
     */
    public function showNews($id)
    {
        $news = \App\Models\News::findOrFail($id);
        return response()->json($news);
    }
}