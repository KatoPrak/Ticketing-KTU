<?php
namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;

class TicketController extends Controller
{
    // Simpan tiket
    public function store(Request $request)
    {
        $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'description'   => 'required|string',
            'attachments.*' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,heif,pdf,doc,docx',
        ]);

        $filePaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filePaths[] = $file->store('tickets', 'public');
            }
        }

        $ticket = Ticket::create([
            'user_id'     => Auth::id(),
            'category_id' => $request->category_id,
            'description' => $request->description,
            'attachments' => json_encode($filePaths),
            'status'      => 'waiting',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tiket berhasil dibuat dengan ID: #' . $ticket->id,
            'ticket'  => $ticket
        ], 201);
    }

    // Tampilkan daftar tiket
    public function index(Request $request)
    {
        $categories = Category::all(); // untuk dropdown kategori

        $query = Ticket::with('category'); // ambil relasi category
       
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
            $query->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('id', $request->search);
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('staff.list-tiket', compact('categories', 'tickets'));
    }
}
