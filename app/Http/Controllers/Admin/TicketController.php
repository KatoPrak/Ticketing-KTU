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
        // ✅ Tambahkan 'feedback' relation untuk load rating & comment
        $query = Ticket::query()->with(['user.department', 'category', 'department', 'feedback']);

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // ✅ Gunakan paginate dengan opsi per_page dari request
        $perPage = $request->input('per_page', 10);
        $tickets = $query->latest()->paginate($perPage)->appends($request->query());

        // Get available years for filter
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
        // ✅ Tambahkan 'feedback' relation untuk export PDF juga
        $query = Ticket::query()->with(['user.department', 'category', 'department', 'feedback']);

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        $tickets = $query->latest()->get();

        // Generate PDF
        $pdf = Pdf::loadView('admin.pdfuser', compact('tickets'))
            ->setPaper('a4', 'landscape'); // ✅ Landscape untuk kolom lebih banyak

        return $pdf->download('tickets-report-' . date('Y-m-d') . '.pdf');
    }

    /**
     * 👥 Menampilkan daftar user dan department (untuk halaman User Management)
     */
    public function showUsers()
    {
        $users = User::with('department')->get();
        $departments = Department::all();

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
     * 🆕 Store new ticket
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'category_id' => 'required|exists:categories,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            $ticket = new Ticket();
            $ticket->user_id = auth()->id();
            $ticket->department_id = auth()->user()->department_id;
            $ticket->category_id = $request->category_id;
            $ticket->title = $request->title;
            $ticket->description = $request->description;
            $ticket->status = 'open';
            $ticket->priority = $request->priority ?? 'low';
            $ticket->save();

            return response()->json([
                'success' => true,
                'message' => 'Ticket created successfully',
                'ticket' => $ticket->load('department', 'category'),
            ]);

        } catch (\Exception $e) {
            \Log::error('Admin create ticket error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket'
            ], 500);
        }
    }

    /**
     * 📊 Get ticket statistics (optional - for dashboard)
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'total' => Ticket::count(),
                'open' => Ticket::where('status', 'open')->count(),
                'in_progress' => Ticket::where('status', 'in_progress')->count(),
                'resolved' => Ticket::where('status', 'resolved')->count(),
                'closed' => Ticket::where('status', 'closed')->count(),
                'with_feedback' => Ticket::has('feedback')->count(),
                'avg_rating' => \DB::table('ticket_feedbacks')->avg('rating'),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics'
            ], 500);
        }
    }
}