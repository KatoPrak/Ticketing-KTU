<?php
namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
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

        // ✅ Balikin JSON supaya cocok sama fetch di user.js
        return response()->json([
            'success' => true,
            'message' => 'Tiket berhasil dibuat dengan ID: #' . $ticket->id,
            'ticket'  => $ticket
        ], 201);
    }
    public function index()
    {
        // kalau cuma mau tampilin view aja
        return view('staff.list-tiket'); 
        // pastikan file ada di resources/views/staff/list-tiket.blade.php
    }

    public function create()
{
    // tampilkan form ticket
    return view('staff.form-tiket'); // pastikan ada file blade ini
}

}
