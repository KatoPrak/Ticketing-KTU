<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Department;

class AdminController extends Controller
{

    public function index(Request $request)
{
    $totalUsers = User::count();
    $newUsersThisMonth = User::whereMonth('created_at', now()->month)
                              ->whereYear('created_at', now()->year)
                              ->count();

    $totalTickets = Ticket::count();

    $pendingTickets = Ticket::where('status', 'pending')->count();
    $inProgressTickets = Ticket::where('status', 'in_progress')->count();
    $resolvedTickets = Ticket::where('status', 'resolved')->count();
    $closedTickets = Ticket::where('status', 'closed')->count();

    $totalCategories = Category::count();
    $categories = Category::withCount('tickets')->get();

    $users = User::orderBy('created_at', 'desc')->paginate(10);

    // Statistika Status untuk Pie Chart
    $statusStats = Ticket::select('status', \DB::raw('count(*) as count'))
        ->groupBy('status')
        ->pluck('count', 'status');

    // Ambil data monthly (existing helper)
    $monthlyTickets = $this->getMonthlyTicketsData();
    // siapkan labels & data untuk Chart.js (aman jika helper berubah)
    $labels = $monthlyTickets['labels'] ?? [];
    $ticketData = $monthlyTickets['data'] ?? [];

    // ambil filter/year dari request (default aman)
    $filter = $request->get('filter', 'month');
    $year = $request->get('year', now()->year);

    // juga kirimkan statistik kategori/department (seperti sebelumnya)
    $categoryStats = $this->getCategoryStatsData();
    $departmentStats = $this->getDepartmentStatsData();

    // 🆕 Latest Tickets for Dashboard Monitoring
    $latestTickets = Ticket::with(['user.location', 'department', 'category'])
        ->latest()
        ->take(5)
        ->get();

    return view('admin.Admin', compact(
        'totalUsers',
        'newUsersThisMonth',
        'totalTickets',
        'pendingTickets',
        'inProgressTickets',
        'resolvedTickets',
        'closedTickets',
        'totalCategories',
        'users',
        'latestTickets', // ✅ Added
        'categories',
        'monthlyTickets',
        'categoryStats',
        'departmentStats',
        // tambahan untuk chart & filter agar Blade tidak undefined
        'labels',
        'ticketData',
        'statusStats',
        'filter',
        'year'
    ));
}

public function showUsers()
{
    $users = User::with(['department', 'location', 'region'])->get();
    $departments = Department::all();
    $locations = \App\Models\Location::orderBy('name')->get();
    $regions = \App\Models\Region::orderBy('name')->get();

    return view('admin.management-pengguna', compact('users', 'departments', 'locations', 'regions'));
}


    public function storeUser(Request $request)
{
    $validated = $request->validate([
        'name'          => 'required|string|max:255',
        'nik'           => 'nullable|string|max:50|unique:users,nik',
        'username'      => 'required|string|max:50|unique:users,username',
        'email'         => 'nullable|email',
        'role'          => 'required|string',
        'department_id' => 'required|exists:departments,id',
        'location_id'   => 'nullable|exists:locations,id',
        'region_id'     => 'nullable|exists:regions,id',
        'password'      => 'required|string|min:6',
    ]);

    $user = new User();
    $user->name          = $validated['name'];
    $user->nik           = $validated['nik'] ? Str::lower($validated['nik']) : null;
    $user->username      = isset($validated['username']) ? Str::lower($validated['username']) : null;
    $user->email         = $validated['email'] ?? null;
    $user->role          = $validated['role'];
    $user->department_id = $validated['department_id'];
    $user->location_id   = $validated['location_id'] ?? null;
    $user->region_id     = $validated['region_id'] ?? null;
    $user->password      = Hash::make($validated['password']);
    $user->save();

    return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan!');
}


    public function updateUser(Request $request, $id)
{
    $user = User::findOrFail($id);

    $validated = $request->validate([
        'name'          => 'required|string|max:255',
        'nik'           => 'nullable|string|max:50|unique:users,nik,' . $user->id,
        'username'      => 'required|string|max:50|unique:users,username,' . $user->id,
        'email'         => 'nullable|email',
        'role'          => 'required|string',
        'department_id' => 'required|exists:departments,id',
        'location_id'   => 'nullable|exists:locations,id',
        'region_id'     => 'nullable|exists:regions,id',
        'password'      => 'nullable|string|min:6',
    ]);

    $updateData = [
        'name'          => $validated['name'],
        'nik'           => $validated['nik'] ? Str::lower($validated['nik']) : null,
        'username'      => isset($validated['username']) ? Str::lower($validated['username']) : null,
        'email'         => $validated['email'] ?? null,
        'role'          => $validated['role'],
        'department_id' => $validated['department_id'],
        'location_id'   => $validated['location_id'] ?? null,
        'region_id'     => $validated['region_id'] ?? null,
    ];

    // Only update password if a new one was submitted
    $newPassword = $request->input('password');
    if (!empty($newPassword)) {
        $updateData['password'] = Hash::make($newPassword);
    }

    $user->update($updateData);

    return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui!');
}


    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->tickets()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete user with existing tickets.'
            ]);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully!'
        ]);
    }

    public function getUser($id)
    {
        $user = User::with('department')->findOrFail($id);
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'nik' => $user->nik,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'department_id' => $user->department_id,
            'location_id' => $user->location_id,
            'region_id' => $user->region_id,
        ]);
    }

    public function storeCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $request->category_id,
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $categoryData = [
            'name' => $request->name,
            'description' => $request->description,
            'icon' => $request->icon,
            'color' => $request->color ?? '#3b82f6',
            'status' => $request->status,
        ];

        if ($request->category_id) {
            Category::findOrFail($request->category_id)->update($categoryData);
            $message = 'Category updated successfully!';
        } else {
            Category::create($categoryData);
            $message = 'Category created successfully!';
        }

        return redirect()->route('admin.dashboard')->with('success', $message);
    }

    public function updateCategory(Request $request, $id)
    {
        $request->merge(['category_id' => $id]);
        return $this->storeCategory($request);
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);

        if ($category->tickets()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with existing tickets.'
            ]);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully!'
        ]);
    }

    public function getCategory($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function exportExcel(Request $request)
    {
        $data = $this->prepareExportData($request);
        $filename = 'admin_data_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            if (isset($data['users'])) {
                fputcsv($file, ['=== USERS ===']);
                fputcsv($file, ['Name', 'Email', 'Department', 'Role', 'Employee ID', 'Status', 'Created At']);
                foreach ($data['users'] as $user) {
                    fputcsv($file, [
                        $user->name,
                        $user->email,
                        $user->department,
                        $user->role,
                        $user->employee_id,
                        $user->status,
                        $user->created_at->format('Y-m-d H:i:s')
                    ]);
                }
                fputcsv($file, ['']);
            }

            if (isset($data['tickets'])) {
                fputcsv($file, ['=== TICKETS ===']);
                fputcsv($file, ['ID', 'Title', 'User', 'Category', 'Priority', 'Status', 'Created At', 'Resolved At']);
                foreach ($data['tickets'] as $ticket) {
                    fputcsv($file, [
                        $ticket->id,
                        $ticket->title,
                        $ticket->user->name ?? '',
                        $ticket->category->name ?? '',
                        $ticket->priority,
                        $ticket->status,
                        $ticket->created_at->format('Y-m-d H:i:s'),
                        $ticket->resolved_at ? $ticket->resolved_at->format('Y-m-d H:i:s') : ''
                    ]);
                }
                fputcsv($file, ['']);
            }

            if (isset($data['categories'])) {
                fputcsv($file, ['=== CATEGORIES ===']);
                fputcsv($file, ['Name', 'Description', 'Tickets Count', 'Status']);
                foreach ($data['categories'] as $category) {
                    fputcsv($file, [
                        $category->name,
                        $category->description,
                        $category->tickets_count,
                        $category->status
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $reportType = $request->reportType ?? 'summary';
        $dateRange = $request->dateRange ?? 'month';

        switch ($dateRange) {
            case 'week':
                $startDate = now()->subWeek();
                break;
            case 'year':
                $startDate = now()->subYear();
                break;
            default:
                $startDate = now()->subMonth();
        }

        $endDate = now();

        $data = [
            'reportType' => $reportType,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'generatedAt' => now(),
            'totalUsers' => 0,
            'activeUsers' => 0,
            'roles' => [],
            'totalTickets' => 0,
            'resolvedTickets' => 0,
            'categoryStats' => [],
            'departmentStats' => [],
        ];

        if ($reportType === 'user') {
            $data['totalUsers'] = User::count();
            $data['activeUsers'] = User::where('status', 'active')->count();
            $data['roles'] = User::select('role', DB::raw('count(*) as total'))
                ->groupBy('role')
                ->get();
        } else {
            $data['totalTickets'] = Ticket::whereBetween('created_at', [$startDate, $endDate])->count();
            $data['resolvedTickets'] = Ticket::where('status', 'resolved')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            $data['categoryStats'] = $this->getCategoryStatsData($startDate, $endDate);
            $data['departmentStats'] = $this->getDepartmentStatsData($startDate, $endDate);
        }

        $html = view('admin.reports.pdf', $data)->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream($reportType . '_report_' . now()->format('Y-m-d') . '.pdf');
    }

    private function prepareExportData(Request $request)
    {
        $data = [];

        if ($request->include_users) {
            $data['users'] = User::orderBy('name')->get();
        }

        if ($request->include_tickets) {
            $data['tickets'] = Ticket::with(['user', 'category'])->orderBy('created_at', 'desc')->get();
        }

        if ($request->include_categories) {
            $data['categories'] = Category::withCount('tickets')->orderBy('name')->get();
        }

        return $data;
    }

    private function getMonthlyTicketsData()
    {
        $months = [];
        $data = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            $data[] = Ticket::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
        }

        return [
            'labels' => $months,
            'data' => $data
        ];
    }

    private function getCategoryStatsData($startDate = null, $endDate = null)
    {
        $palette = [
            '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', 
            '#ec4899', '#06b6d4', '#84cc16', '#f97316', '#6366f1'
        ];

        $query = Category::withCount(['tickets' => function ($q) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        }]);

        return $query->get()->map(function ($category, $index) use ($palette) {
            return [
                'name' => $category->name,
                'count' => $category->tickets_count,
                'color' => $category->color ?: $palette[$index % count($palette)]
            ];
        });
    }

   private function getDepartmentStatsData($startDate = null, $endDate = null)
{
    $query = DB::table('tickets as t')
        ->join('users as u', 't.user_id', '=', 'u.id')
        ->join('departments as d', 'u.department_id', '=', 'd.id') // join department
        ->select('d.name as department_name', DB::raw('count(*) as count'));

    if ($startDate && $endDate) {
        $query->whereBetween('t.created_at', [$startDate, $endDate]);
    }

    return $query->groupBy('d.name')->get();
}


    public function getChartData(Request $request)
    {
        $period = $request->period ?? 30;
        $startDate = now()->subDays($period);
        $endDate = now();

        return response()->json([
            'monthly' => $this->getMonthlyTicketsData(),
            'categories' => $this->getCategoryStatsData($startDate, $endDate),
            'departments' => $this->getDepartmentStatsData($startDate, $endDate),
        ]);
    }

        public function dashboard(Request $request)
        {
            $totalUsers = User::count();
            $totalTickets = Ticket::count();
            $pendingTickets = Ticket::where('status', 'pending')->count();

            $filter = $request->get('filter', 'month');
            $year = $request->get('year', now()->year);

            switch ($filter) {
                case 'week':
                    $startDate = now()->startOfWeek();
                    $endDate = now()->endOfWeek();
                    $chartType = 'bar';
                    break;

                case 'year':
                    $startDate = now()->startOfYear();
                    $endDate = now()->endOfYear();
                    $chartType = 'line';
                    break;

                default: // month
                    $startDate = now()->startOfMonth();
                    $endDate = now()->endOfMonth();
                    $chartType = 'line';
                    break;
            }

            $tickets = Ticket::whereBetween('created_at', [$startDate, $endDate])->get();

            $labels = [];
            $ticketData = [];

            if ($filter === 'year') {
                $labels = collect(range(1, 12))->map(fn($m) => Carbon::create()->month($m)->format('M'));
                $ticketData = collect(range(1, 12))->map(fn($m) =>
                    $tickets->whereBetween('created_at', [
                        Carbon::create($year, $m, 1)->startOfMonth(),
                        Carbon::create($year, $m, 1)->endOfMonth()
                    ])->count()
                );
            } else {
                $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
                foreach ($period as $date) {
                    $labels[] = $date->format('d M');
                    $ticketData[] = $tickets->whereBetween('created_at', [
                        $date->copy()->startOfDay(),
                        $date->copy()->endOfDay()
                    ])->count();
                }
            }

            // pastikan view-nya sesuai file kamu
            return view('admin.Admin', compact(
                'totalUsers',
                'totalTickets',
                'pendingTickets',
                'labels',
                'ticketData',
                'year',
                'filter',
                'chartType'
            ));
        }
        }
