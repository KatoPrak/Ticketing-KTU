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
        $filter = $request->get('filter', 'year'); // default year

        $query = Ticket::whereYear('created_at', $year);

        if ($filter === 'week') {
            $ticketsByDay = $query->selectRaw('DAYOFWEEK(created_at) as day, COUNT(*) as total')
                ->groupBy('day')
                ->pluck('total', 'day');

            $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

            $labels = [];
            $ticketData = [];
            foreach (range(1, 7) as $d) {
                $labels[] = $days[$d-1];
                $ticketData[] = $ticketsByDay[$d] ?? 0;
            }

            $chartType = 'bar';
        } else {
            $ticketsByMonth = $query->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->groupBy('month')
                ->pluck('total', 'month');

            $labels = collect(range(1, 12))->map(function ($m) {
                return Carbon::create()->month($m)->format('F');
            })->toArray();

            $ticketData = [];
            foreach (range(1, 12) as $m) {
                $ticketData[] = $ticketsByMonth[$m] ?? 0;
            }

            $chartType = 'line';
        }

        return view('admin.reports', compact('labels', 'ticketData', 'year', 'filter', 'chartType'));
    }
}
