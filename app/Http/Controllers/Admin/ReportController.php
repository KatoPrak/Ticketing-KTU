<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Menampilkan daftar semua laporan.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);
        $regionId = $request->get('region_id');

        // Base query for stats
        $query = Ticket::query()
            ->when($year, fn($q) => $q->whereYear('created_at', $year))
            ->when($month, fn($q) => $q->whereMonth('created_at', $month))
            ->when($regionId, fn($q) => $q->where('region_id', $regionId));

        // 1. Regional Breakdown
        $regionalStats = \App\Models\Region::withCount(['tickets' => function($q) use ($year, $month) {
            $q->whereYear('created_at', $year)
              ->whereMonth('created_at', $month);
        }])->get();

        // 2. IT Staff Performance (Who worked on it)
        $itStaffPerformance = \App\Models\User::whereIn('role', ['tim it', 'it'])
            ->withCount(['tickets as total_assigned' => function($q) use ($year, $month) {
                $q->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month);
            }])
            ->withCount(['tickets as resolved_count' => function($q) use ($year, $month) {
                $q->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month)
                  ->where('status', 'resolved');
            }])
            ->having('total_assigned', '>', 0)
            ->get();

        // 3. Status Breakdown for Chart
        $statusCounts = (clone $query)->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $statusLabels = $statusCounts->keys()->map(fn($s) => ucfirst(str_replace('_', ' ', $s)))->toArray();
        $statusData = $statusCounts->values()->toArray();

        // 4. Monthly Trend (For the selected year/region)
        $trendQuery = Ticket::whereYear('created_at', $year)
            ->when($regionId, fn($q) => $q->where('region_id', $regionId))
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $months = collect(range(1, 12))->map(fn($m) => Carbon::create()->month($m)->format('M'));
        $trendData = collect(range(1, 12))->map(fn($m) => $trendQuery[$m] ?? 0);

        $regions = \App\Models\Region::all();

        return view('admin.reports', compact(
            'year', 'month', 'regionId', 'regions',
            'regionalStats', 'itStaffPerformance',
            'statusLabels', 'statusData',
            'months', 'trendData'
        ));
    }
}
