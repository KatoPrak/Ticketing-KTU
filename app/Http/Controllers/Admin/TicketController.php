<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Department;
use App\Models\Region;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    
    /**
     * 🧾 Menampilkan daftar semua tiket dengan filter tahun dan bulan.
     */
    public function index(Request $request)
    {
        // ✅ Tambahkan 'feedback' & 'transferLogs' relation
        $query = Ticket::query()->with(['user.department', 'category', 'department', 'feedback', 'transferLogs']);

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
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

        // 🗺️ Ambil semua region untuk filter
        $regions = Region::orderBy('name')->get();

        return view('admin.tickets', compact('tickets', 'years', 'departments', 'regions'));
    }

    /**
     * 🧩 Export data tiket ke PDF (mengikuti filter yang aktif).
     */
    public function exportPdf(Request $request)
    {
        // ✅ Tambahkan 'feedback', 'assignedTo', dan 'region' relation
        $query = Ticket::query()->with([
            'user.department', 
            'user.location', 
            'category', 
            'department', 
            'feedback',
            'assignedTo',
            'region',
            'transferLogs'
        ]);

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        $tickets = $query->latest()->get();

        // Ambil info region jika difilter
        $selectedRegion = null;
        if ($request->filled('region_id')) {
            $selectedRegion = Region::find($request->region_id);
        }

        // Generate PDF
        $pdf = Pdf::loadView('admin.pdfuser', compact('tickets', 'selectedRegion'))
            ->setPaper('a4', 'landscape'); // ✅ Landscape untuk kolom lebih banyak

        // ✅ Stream PDF untuk preview di browser (bukan langsung download)
        return $pdf->stream('tickets-report-' . date('Y-m-d') . '.pdf');
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
            'nik' => $user->nik,
            'username' => $user->username,
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