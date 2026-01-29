<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TicketFeedback;
use App\Models\Ticket;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FeedbackController extends Controller
{
    /**
     * Display feedbacks dashboard page
     */
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        
        return view('it.feedbacks', compact('categories'));
    }

    /**
     * Get dashboard statistics
     */
    public function getStats()
    {
        try {
            // Average Rating
            $avgRating = TicketFeedback::avg('rating') ?? 0;

            // Total Feedbacks
            $totalFeedbacks = TicketFeedback::count();

            // Satisfaction Rate (4-5 stars)
            $satisfiedCount = TicketFeedback::whereIn('rating', [4, 5])->count();
            $satisfactionRate = $totalFeedbacks > 0 ? round(($satisfiedCount / $totalFeedbacks) * 100) : 0;

            // This Month Count
            $thisMonthCount = TicketFeedback::whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->count();

            // Rating Distribution
            $distribution = [];
            for ($i = 1; $i <= 5; $i++) {
                $distribution[$i] = TicketFeedback::where('rating', $i)->count();
            }

            // Trend Data (Last 6 Months)
            $trends = $this->getTrendData();

            return response()->json([
                'success' => true,
                'stats' => [
                    'avgRating' => round($avgRating, 1),
                    'totalFeedbacks' => $totalFeedbacks,
                    'satisfactionRate' => $satisfactionRate,
                    'thisMonthCount' => $thisMonthCount,
                    'distribution' => $distribution,
                    'trends' => $trends
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Feedback stats error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics'
            ], 500);
        }
    }

    /**
     * Get trend data for chart
     */
    private function getTrendData()
    {
        $months = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            $avgRating = TicketFeedback::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->avg('rating');
            
            $data[] = $avgRating ? round($avgRating, 1) : 0;
        }

        return [
            'labels' => $months,
            'data' => $data
        ];
    }

    /**
     * Get feedbacks list with filters
     */
    public function getFeedbacksList(Request $request)
    {
        try {
            $query = TicketFeedback::with(['ticket.user.department', 'ticket.category', 'user']);

            // Filter by rating
            if ($request->filled('rating')) {
                $query->where('rating', $request->rating);
            }

            // Filter by category
            if ($request->filled('category')) {
                $query->whereHas('ticket', function($q) use ($request) {
                    $q->where('category_id', $request->category);
                });
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Pagination
            $perPage = 10;
            $feedbacks = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Format data
            $formattedFeedbacks = $feedbacks->map(function($feedback) {
                return [
                    'id' => $feedback->id,
                    'ticket_id' => $feedback->ticket->ticket_id ?? 'N/A',
                    'user_name' => $feedback->ticket->user->name ?? 'N/A',
                    'department' => $feedback->ticket->user->department->name ?? 'N/A',
                    'category' => $feedback->ticket->category->name ?? 'N/A',
                    'rating' => $feedback->rating,
                    'comment' => $feedback->comment,
                    'created_at' => $feedback->created_at->timezone('Asia/Jakarta')->format('d M Y H:i')
                ];
            });

            return response()->json([
                'success' => true,
                'feedbacks' => $formattedFeedbacks,
                'pagination' => [
                    'current_page' => $feedbacks->currentPage(),
                    'last_page' => $feedbacks->lastPage(),
                    'per_page' => $feedbacks->perPage(),
                    'total' => $feedbacks->total(),
                    'from' => $feedbacks->firstItem(),
                    'to' => $feedbacks->lastItem()
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Feedback list error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load feedbacks'
            ], 500);
        }
    }

    /**
     * Export feedbacks to Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            $query = TicketFeedback::with(['ticket.user.department', 'ticket.category', 'user']);

            // Apply same filters as list
            if ($request->filled('rating')) {
                $query->where('rating', $request->rating);
            }
            if ($request->filled('category')) {
                $query->whereHas('ticket', function($q) use ($request) {
                    $q->where('category_id', $request->category);
                });
            }
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $feedbacks = $query->orderBy('created_at', 'desc')->get();

            // Create CSV content
            $filename = 'feedbacks_' . date('Y-m-d_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($feedbacks) {
                $file = fopen('php://output', 'w');
                
                // Add UTF-8 BOM for Excel
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Header row
                fputcsv($file, [
                    'Ticket ID',
                    'User',
                    'Department',
                    'Category',
                    'Rating',
                    'Comment',
                    'Date'
                ]);

                // Data rows
                foreach ($feedbacks as $feedback) {
                    fputcsv($file, [
                        $feedback->ticket->ticket_id ?? 'N/A',
                        $feedback->ticket->user->name ?? 'N/A',
                        $feedback->ticket->user->department->name ?? 'N/A',
                        $feedback->ticket->category->name ?? 'N/A',
                        $feedback->rating,
                        $feedback->comment,
                        $feedback->created_at->timezone('Asia/Jakarta')->format('d M Y H:i')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            \Log::error('Export feedbacks error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export feedbacks');
        }
    }

    /**
     * Get feedback details
     */
    public function show($id)
    {
        try {
            $feedback = TicketFeedback::with(['ticket.user.department', 'ticket.category'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'feedback' => [
                    'id' => $feedback->id,
                    'ticket_id' => $feedback->ticket->ticket_id ?? 'N/A',
                    'user_name' => $feedback->ticket->user->name ?? 'N/A',
                    'department' => $feedback->ticket->user->department->name ?? 'N/A',
                    'category' => $feedback->ticket->category->name ?? 'N/A',
                    'rating' => $feedback->rating,
                    'comment' => $feedback->comment,
                    'created_at' => $feedback->created_at->timezone('Asia/Jakarta')->format('d M Y H:i')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Feedback not found'
            ], 404);
        }
    }
}