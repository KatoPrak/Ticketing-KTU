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
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index()
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
        
        $monthlyTickets = $this->getMonthlyTicketsData();
        $categoryStats = $this->getCategoryStatsData();
        $departmentStats = $this->getDepartmentStatsData();

        // Mengubah jalur tampilan agar sesuai dengan file Anda yang ada
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
            'categories',
            'monthlyTickets',
            'categoryStats',
            'departmentStats'
        ));
    }

    public function showUsers()
    {
        $users = User::all();
        return view('admin.management-pengguna', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'id_staff' => 'required|string|max:50|unique:users,id_staff',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string',
            'department' => 'required|string',
        ]);
    
        $user = new User();
        $user->name = $validated['name'];
        $user->id_staff = $validated['id_staff'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->department = $validated['department'];
        $user->password = Hash::make($request->input('password', 'STAFFKTU123'));
        $user->save();
    
        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan!');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
    
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'id_staff' => 'required|string|max:50|unique:users,id_staff,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|string',
            'department' => 'required|string',
        ]);
    
        $user->update([
            'name' => $validated['name'],
            'id_staff' => $validated['id_staff'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'department' => $validated['department'],
        ]);
    
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
        $user = User::findOrFail($id);
        return response()->json($user);
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

        $callback = function() use ($data) {
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
        $reportType = $request->report_type ?? 'summary';
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->subMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : now();

        $data = [
            'reportType' => $reportType,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalUsers' => User::count(),
            'totalTickets' => Ticket::whereBetween('created_at', [$startDate, $endDate])->count(),
            'resolvedTickets' => Ticket::where('status', 'resolved')
                                    ->whereBetween('created_at', [$startDate, $endDate])
                                    ->count(),
            'categoryStats' => $this->getCategoryStatsData($startDate, $endDate),
            'departmentStats' => $this->getDepartmentStatsData($startDate, $endDate),
            'generatedAt' => now(),
        ];

        $html = view('admin.reports.pdf', $data)->render();
        
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        return $dompdf->stream('admin_report_' . now()->format('Y-m-d') . '.pdf');
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
        $query = Category::withCount(['tickets' => function ($q) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        }]);

        return $query->get()->map(function ($category) {
            return [
                'name' => $category->name,
                'count' => $category->tickets_count,
                'color' => $category->color ?? '#3b82f6'
            ];
        });
    }

    private function getDepartmentStatsData($startDate = null, $endDate = null)
    {
        $query = DB::table('tickets')
                ->join('users', 'tickets.user_id', '=', 'users.id')
                ->select('users.department', DB::raw('count(*) as count'));

        if ($startDate && $endDate) {
            $query->whereBetween('tickets.created_at', [$startDate, $endDate]);
        }

        return $query->groupBy('users.department')->get();
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
}
