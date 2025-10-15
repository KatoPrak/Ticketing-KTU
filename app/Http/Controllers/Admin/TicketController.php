<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Department;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * 🧾 Menampilkan daftar semua tiket dengan filter tahun dan bulan.
     */
    public function index(Request $request)
    {
        $query = Ticket::query()->with(['user.department', 'category', 'department']);

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        $tickets = $query->latest()->get();

        $years = Ticket::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // 🏢 Ambil semua departemen untuk dropdown di modal
        $departments = Department::orderBy('name')->get();

        return view('admin.tickets', compact('tickets', 'years', 'departments'));
    }

    /**
     * 🧩 Export data tiket ke PDF (mengikuti filter yang aktif).
     */
    public function exportPdf(Request $request)
    {
        $query = Ticket::query()->with(['user.department', 'category', 'department']);

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        $tickets = $query->latest()->get();

        $pdf = Pdf::loadView('admin.pdfuser', compact('tickets'));
        return $pdf->download('tickets-report-' . date('Y-m-d') . '.pdf');
    }

    /**
     * 👥 Menampilkan daftar user dan department (untuk halaman User Management)
     */
public function showUsers()
{
    $users = User::with('department')->get();
    $departments = Department::all(); // ⬅️ ini kuncinya

    return view('admin.management-pengguna', compact('users', 'departments'));
}

    /**
     * 📦 Mengambil data user untuk modal edit (AJAX)
     */
    public function getUser($id)
    {
        $user = User::with('department')->find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'id_staff' => $user->id_staff,
            'email' => $user->email,
            'role' => $user->role,
            'department_id' => $user->department_id,
        ]);
    }

    /**
     * 🆕 Tambahan opsional: Simpan ticket baru (kalau nanti mau pakai create)
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $ticket = new Ticket();
        $ticket->user_id = auth()->id();
        $ticket->department_id = auth()->user()->department_id; // otomatis ambil dari user login
        $ticket->category_id = $request->category_id;
        $ticket->title = $request->title;
        $ticket->description = $request->description;
        $ticket->status = 'open';
        $ticket->save();

        return response()->json([
            'success' => true,
            'message' => 'Ticket created successfully',
            'ticket' => $ticket->load('department', 'category'),
        ]);
    }
}
