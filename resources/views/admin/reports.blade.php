@extends('layouts.admin')

@section('title', 'System Reports')

@section('content')
<div class="container-fluid py-4">
    <!-- Header/Filters -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">
                        <i class="fas fa-chart-pie text-primary me-2"></i>System Analytics
                    </h4>
                    <p class="text-muted small mb-0">Detailed performance reports by region and staff.</p>
                </div>
                
                <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-2 align-items-center">
                    <div class="col-auto">
                        <select name="region_id" class="form-select border-0 bg-light rounded-pill px-3 shadow-none" onchange="this.form.submit()">
                            <option value="">All Regions</option>
                            @foreach($regions as $r)
                                <option value="{{ $r->id }}" @selected($regionId == $r->id)>{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <select name="month" class="form-select border-0 bg-light rounded-pill px-3 shadow-none" onchange="this.form.submit()">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" @selected($month == $m)>
                                    {{ Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <select name="year" class="form-select border-0 bg-light rounded-pill px-3 shadow-none" onchange="this.form.submit()">
                            @foreach(range(now()->year, now()->year - 5) as $y)
                                <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-light rounded-circle shadow-none" title="Reset">
                            <i class="fas fa-redo-alt text-muted"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Chart: Monthly Trend -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold text-dark mb-0">Monthly Ticket Trend</h6>
                    <small class="text-muted">Tracking total volume in {{ $year }}</small>
                </div>
                <div class="card-body p-4">
                    <canvas id="trendChart" height="280"></canvas>
                </div>
            </div>
        </div>

        <!-- Side Chart: Status Distribution -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold text-dark mb-0">Status Distribution</h6>
                    <small class="text-muted">Current period breakdown</small>
                </div>
                <div class="card-body p-4 d-flex align-items-center justify-content-center">
                    <div style="width: 100%; max-width: 250px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Regional Performance Table -->
        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold text-dark mb-0">Regional Workload</h6>
                    <small class="text-muted">Tickets per region this month</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light small text-uppercase text-muted">
                                <tr>
                                    <th class="ps-4 border-0">Region</th>
                                    <th class="text-center border-0">Total Tickets</th>
                                    <th class="pe-4 border-0">Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalRegTickets = $regionalStats->sum('tickets_count'); @endphp
                                @forelse($regionalStats as $stat)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-map-marker-alt small"></i>
                                            </div>
                                            <span class="fw-semibold">{{ $stat->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold text-dark">{{ $stat->tickets_count }}</td>
                                    <td class="pe-4">
                                        @php $percent = $totalRegTickets > 0 ? round(($stat->tickets_count / $totalRegTickets) * 100) : 0; @endphp
                                        <div class="progress rounded-pill" style="height: 6px;">
                                            <div class="progress-bar bg-primary" style="width: {{ $percent }}%"></div>
                                        </div>
                                        <small class="text-muted mt-1 d-block">{{ $percent }}%</small>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center py-4 text-muted">No regional data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- IT Staff Performance Table (Who worked on it) -->
        <div class="col-md-7">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold text-dark mb-0">IT Staff Performance</h6>
                    <small class="text-muted">Individual resolution tracking (this month)</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light small text-uppercase text-muted">
                                <tr>
                                    <th class="ps-4 border-0">IT Specialist</th>
                                    <th class="text-center border-0">Assigned</th>
                                    <th class="text-center border-0">Resolved</th>
                                    <th class="pe-4 border-0">Efficiency</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($itStaffPerformance as $staff)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle p-2 bg-info bg-opacity-10 text-info rounded-pill me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="fas fa-user-tie small"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark small">{{ $staff->name }}</div>
                                                <div class="text-uppercase extra-small text-muted" style="font-size: 10px;">{{ $staff->role }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center fw-semibold text-primary">{{ $staff->total_assigned }}</td>
                                    <td class="text-center fw-semibold text-success">{{ $staff->resolved_count }}</td>
                                    <td class="pe-4">
                                        @php $efficiency = $staff->total_assigned > 0 ? round(($staff->resolved_count / $staff->total_assigned) * 100) : 0; @endphp
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1 me-2">
                                                <div class="progress rounded-pill" style="height: 6px;">
                                                    <div class="progress-bar bg-success" style="width: {{ $efficiency }}%"></div>
                                                </div>
                                            </div>
                                            <span class="fw-bold small {{ $efficiency >= 70 ? 'text-success' : 'text-warning' }}">{{ $efficiency }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-4 text-muted">No staff activity recorded this period.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .extra-small { font-size: 0.7rem; }
    .table thead th { font-weight: 600; letter-spacing: 0.5px; }
    .transition-hover { transition: transform 0.2s ease; }
    .transition-hover:hover { transform: translateY(-2px); }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Trend Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: @json($months),
            datasets: [{
                label: 'Monthly Tickets',
                data: @json($trendData),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.05)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#0d6efd',
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, color: '#94a3b8' }, grid: { borderDash: [5, 5] } },
                x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
            }
        }
    });

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: @json($statusLabels),
            datasets: [{
                data: @json($statusData),
                backgroundColor: ['#6c757d', '#ffc107', '#0dcaf0', '#198754', '#dc3545'],
                hoverOffset: 4,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            cutout: '75%',
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, font: { size: 11 } } }
            }
        }
    });
});
</script>
@endpush
@endsection
