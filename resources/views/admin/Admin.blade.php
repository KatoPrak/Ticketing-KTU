@extends('layouts.admin')

@section('title', 'Dashboard')

{{-- ❌ HAPUS INI - Sudah di-load di layout --}}
{{-- <script src="{{ asset('build/assets/admin-DDuIwRwy.js') }}"></script> --}}

@section('content')
    <!-- Dashboard Section -->
    <div id="dashboard" class="content-section active">

        <!-- Welcome Section -->
        <div class="welcome-section mb-4">
            <div class="welcome-content">
                <div class="welcome-text">
                    <h2>Welcome, {{ Auth::user()->name }}!</h2>
                    <p>Have a great day at work! Here's today's system summary.</p>
                    <div class="current-time" id="currentTime"></div>
                </div>
                <div class="welcome-icon">
                    <i class="fas fa-hand-wave"></i>
                </div>
            </div>
        </div>

        <!-- 📊 Statistics Section -->
        <div class="stats-grid mb-5">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $totalUsers }}</h3>
                    <p>Total Users</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $totalTickets }}</h3>
                    <p>Total Tickets</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $pendingTickets }}</h3>
                    <p>Pending Tickets</p>
                </div>
            </div>
        </div>

        <!-- 🎯 Ticket Chart Filter -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Ticket Chart Filter</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.dashboard') }}" class="form-row">
                    <div class="form-group mr-3">
                        <label class="form-label">Filter By</label>
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

        <!-- 📈 Tickets Chart -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tickets Created ({{ $year ?? now()->year }})</h3>
            </div>
            <div class="card-body">
                <canvas id="ticketsChart" height="100"></canvas>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Update Current Time
        function updateTime() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            const timeString = now.toLocaleDateString('en-US', options);
            const timeElement = document.getElementById('currentTime');
            if (timeElement) {
                timeElement.textContent = timeString;
            }
        }

        // Update time immediately and every second
        updateTime();
        setInterval(updateTime, 1000);

        // Tickets Chart
        const ctx = document.getElementById('ticketsChart');
        if (ctx) {
            new Chart(ctx.getContext('2d'), {
                type: '{{ $chartType ?? "line" }}',
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
                    maintainAspectRatio: true,
                    scales: {
                        y: { 
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    }
                }
            });
        }
    });
</script>
@endpush