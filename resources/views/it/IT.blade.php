@extends('layouts.it')

@section('title', 'Dashboard - Tim IT')

@section('content')
    <!-- Welcome Card -->
    <div class="welcome-card">
        <div class="row align-items-center">
            <div class="col-md-8 col-12">
                <h2><i class="fas fa-wave-square me-2"></i>Selamat Datang!</h2>
                <p class="mb-0">{{ Auth::user()->name }} (ID-{{ Auth::user()->id_staff }})</p>
            </div>
            <div class="col-md-4 col-12 text-center text-md-end mt-3 mt-md-0">
                <div class="fs-1">
                    <i class="fas fa-user-tie"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6 col-6">
            <div class="dashboard-card stat-card">
                <div class="stat-number">24</div>
                <div class="stat-label">Active Tickets</div>
                <i class="fas fa-ticket-alt text-primary fs-4 mt-2"></i>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6">
            <div class="dashboard-card stat-card">
                <div class="stat-number">8</div>
                <div class="stat-label">Pending</div>
                <i class="fas fa-clock text-warning fs-4 mt-2"></i>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6">
            <div class="dashboard-card stat-card">
                <div class="stat-number">156</div>
                <div class="stat-label">Completed</div>
                <i class="fas fa-check-circle text-success fs-4 mt-2"></i>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6">
            <div class="dashboard-card stat-card">
                <div class="stat-number">3</div>
                <div class="stat-label">Urgent</div>
                <i class="fas fa-exclamation-triangle text-danger fs-4 mt-2"></i>
            </div>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="row">
        <div class="col-lg-8 col-12">
            <div class="dashboard-card">
                <h5 class="mb-3"><i class="fas fa-chart-line me-2"></i>Recent Activity</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Ticket ID</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>#TKT-001</strong></td>
                                <td>Network Issue</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td><span class="badge bg-danger">High</span></td>
                                <td>2024-08-22</td>
                            </tr>
                            <tr>
                                <td><strong>#TKT-002</strong></td>
                                <td>Software Installation</td>
                                <td><span class="badge bg-primary">In Progress</span></td>
                                <td><span class="badge bg-info">Medium</span></td>
                                <td>2024-08-21</td>
                            </tr>
                            <tr>
                                <td><strong>#TKT-003</strong></td>
                                <td>Hardware Repair</td>
                                <td><span class="badge bg-success">Resolved</span></td>
                                <td><span class="badge bg-secondary">Low</span></td>
                                <td>2024-08-20</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-12">
            <div class="dashboard-card">
                <h5 class="mb-3"><i class="fas fa-tasks me-2"></i>Quick Actions</h5>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary-custom" onclick="createTicket()">
                        <i class="fas fa-plus me-2"></i>Create New Ticket
                    </button>
                    <button class="btn btn-outline-primary" onclick="searchTickets()">
                        <i class="fas fa-search me-2"></i>Search Tickets
                    </button>
                    <button class="btn btn-outline-secondary" onclick="exportReports()">
                        <i class="fas fa-download me-2"></i>Export Reports
                    </button>
                    <button class="btn btn-outline-info" onclick="manageUsers()">
                        <i class="fas fa-users me-2"></i>Manage Users
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
