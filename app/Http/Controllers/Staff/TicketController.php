<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;

class TicketController extends Controller
{
    /**
     * Simpan tiket baru ke database dan buat ID kustom.
     */
    public function store(Request $request)
    {
        // MODIFIKASI: Validasi disederhanakan, tidak lagi memerlukan ticket_id dari form
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'attachments.*' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,heif,pdf,doc,docx',
        ]);

        $filePaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filePaths[] = $file->store('tickets', 'public');
            }
        }

        // --- MULAI LOGIKA BARU ---

        // 1. Buat tiket dengan data awal (kolom ticket_id masih kosong)
        $ticket = Ticket::create([
            'user_id'     => Auth::id(),
            'category_id' => $request->category_id,
            'description' => $request->description,
            'attachments' => json_encode($filePaths),
            'status'      => 'waiting',
            'priority'    => $request->priority ?? 'medium',
        ]);

        // 2. Buat ticket_id kustom berdasarkan ID auto-increment yang baru saja didapat
        $formattedId = 'T-KTU-' . str_pad($ticket->id, 3, '0', STR_PAD_LEFT);

        // 3. Update kembali tiket yang baru dibuat untuk menyimpan ID kustom
        $ticket->ticket_id = $formattedId;
        $ticket->save();

        // --- SELESAI LOGIKA BARU ---

        return response()->json([
            'success' => true,
            'message' => 'Tiket berhasil dibuat dengan ID: ' . $ticket->ticket_id,
            'ticket'  => $ticket
        ], 201);
    }

    /**
     * Tampilkan daftar tiket.
     */
    public function index(Request $request)
{
    $categories = Category::all();

    $query = Ticket::with('category')
        ->where('user_id', Auth::id());

    // Filter status
    if ($request->status) {
        $query->where('status', $request->status);
    }

    // Filter kategori
    if ($request->category_id) {
        $query->where('category_id', $request->category_id);
    }

    // Search berdasarkan description atau id tiket
    if ($request->search) {
        $query->where(function ($q) use ($request) {
            $q->where('description', 'like', '%' . $request->search . '%')
                ->orWhere('id', 'like', '%' . $request->search . '%')
                ->orWhere('ticket_id', 'like', '%' . $request->search . '%');
        });
    }

    // Ambil tiket, decode attachments JSON
    $tickets = $query->orderBy('created_at', 'desc')
                        ->paginate(10)
                        ->through(function ($ticket) {
                        $ticket->attachments = json_decode($ticket->attachments, true) ?? [];
                        return $ticket;
                    });

    return view('staff.list-tiket', compact('categories', 'tickets'));
}

}
