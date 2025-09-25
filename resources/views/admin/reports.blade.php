@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
{{-- Card 1: Generate Report --}}
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Generate Reports</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reports.index') }}">
            <div class="form-row">
                <div class="form-group mr-3">
                    <label class="form-label">Report Type</label>
                    <select class="form-control" name="reportType">
                        <option value="tickets" {{ request('reportType') == 'tickets' ? 'selected' : '' }}>Tickets Report</option>
                        <option value="users" {{ request('reportType') == 'users' ? 'selected' : '' }}>Users Report</option>
                        <option value="summary" {{ request('reportType') == 'summary' ? 'selected' : '' }}>Summary Report</option>
                    </select>
                </div>
                <div class="form-group mr-3">
                    <label class="form-label">Date Range</label>
                    <select class="form-control" name="dateRange">
                        <option value="week" {{ request('dateRange') == 'week' ? 'selected' : '' }}>Last Week</option>
                        <option value="month" {{ request('dateRange') == 'month' ? 'selected' : '' }}>Last Month</option>
                        <option value="year" {{ request('dateRange') == 'year' ? 'selected' : '' }}>Last Year</option>
                    </select>
                </div>
                <div class="form-group align-self-end">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Generate PDF Report
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Card 2: Filter Grafik --}}
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Filter Grafik Tickets</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="form-row">
            <div class="form-group mr-3">
                <label class="form-label">Filter</label>
                <select class="form-control" name="filter">
                    <option value="week" {{ request('filter') == 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ request('filter') == 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="year" {{ request('filter') == 'year' ? 'selected' : '' }}>This Year</option>
                </select>
            </div>
            <div class="form-group mr-3">
                <label class="form-label">Year</label>
                <select class="form-control" name="year">
                    @foreach(range(now()->year, now()->year - 5) as $y)
                        <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group align-self-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Apply
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Card 3: Grafik --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Tickets Added ({{ $year }})</h3>
    </div>
    <div class="card-body">
        <canvas id="ticketsChart" height="100"></canvas>
    </div>
</div>

    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">
                Tickets Added ({{ $filter === 'week' ? 'This Week' : ($filter === 'month' ? 'This Month' : $year) }})
            </h3>
        </div>
        <div class="card-body">
            <canvas id="ticketsChart" height="100"></canvas>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('ticketsChart').getContext('2d');
            new Chart(ctx, {
                type: '{{ $chartType ?? "line" }}', // chart type sesuai controller
                data: {
                    labels: @json($labels),
                    datasets: [{
                        label: 'Tickets',
                        data: @json($ticketData),
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });
    </script>
@endsection
