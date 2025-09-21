<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>IT Dashboard - KTU Shipyard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('assets/image/logo-ktu.jpg') }}">
    @vite(['resources/css/it.css'])

</head>

<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <!-- Mobile Sidebar Toggle -->
            <button class="btn navbar-toggler d-lg-none me-2" type="button" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Brand -->
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <img src="{{ asset('assets/image/logo-ktu-removebg.png') }}" alt="KTU Logo" class="me-2"
                    style="height: 40px; width: auto; object-fit: contain;">
                <span class="fw-bold">IT Support</span>
            </a>

            <!-- Navbar Right -->
            <div class="navbar-nav ms-auto">
                <!-- Notifications -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell me-1"></i>
                        <span class="badge bg-danger rounded-pill">3</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-ticket-alt me-2"></i>Ticket Baru</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-exclamation-triangle me-2"></i>Urgent
                                Request</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-check-circle me-2"></i>Ticket
                                Resolved</a></li>
                    </ul>
                </div>
      <!-- User Profile -->
<div class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
        <i class="fas fa-user-circle me-1"></i>
        <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end">
        <li>
            <a class="dropdown-item" href="#">
                <i class="fas fa-user me-2"></i> Profile
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="#">
                <i class="fas fa-cog me-2"></i> Settings
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
    <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); logout();">
        <i class="fas fa-sign-out-alt me-2"></i> Logout
    </a>

    <!-- Form logout tersembunyi -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</li>

    </ul>
</div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h5><i class="fas fa-tachometer-alt me-2"></i>Dashboard Menu</h5>
        </div>

        <ul class="sidebar-menu">
            <li>
                <a href="#" class="active">
                    <i class="fas fa-home"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="#" onclick="showPage('tickets')">
                    <i class="fas fa-ticket-alt"></i>
                    Tickets
                </a>
            </li>
            <li>
                <a href="#" onclick="showPage('create-ticket')">
                    <i class="fas fa-plus-circle"></i>
                    Create Ticket
                </a>
            </li>
            <li>
                <a href="#" onclick="showPage('users')">
                    <i class="fas fa-users"></i>
                    Users
                </a>
            </li>
            <li>
                <a href="#" onclick="showPage('assets')">
                    <i class="fas fa-laptop"></i>
                    Assets
                </a>
            </li>
            <li>
                <a href="#" onclick="showPage('reports')">
                    <i class="fas fa-chart-bar"></i>
                    Reports
                </a>
            </li>
            <li>
                <a href="#" onclick="showPage('maintenance')">
                    <i class="fas fa-tools"></i>
                    Maintenance
                </a>
            </li>
            <li>
                <a href="#" onclick="showPage('settings')">
                    <i class="fas fa-cog"></i>
                    Settings
                </a>
            </li>
            <li>
                <a href="#" onclick="showPage('help')">
                    <i class="fas fa-question-circle"></i>
                    Help & Support
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
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
                                <tr>
                                    <td><strong>#TKT-004</strong></td>
                                    <td>Email Configuration</td>
                                    <td><span class="badge bg-primary">In Progress</span></td>
                                    <td><span class="badge bg-info">Medium</span></td>
                                    <td>2024-08-19</td>
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

                <!-- System Status Card -->
                <div class="dashboard-card mt-3">
                    <h5 class="mb-3"><i class="fas fa-server me-2"></i>System Status</h5>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="text-success">
                                <i class="fas fa-check-circle fs-3"></i>
                                <p class="mb-0 mt-2"><small>All Systems</small></p>
                                <strong>Operational</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-info">
                                <i class="fas fa-clock fs-3"></i>
                                <p class="mb-0 mt-2"><small>Response Time</small></p>
                                <strong>1.2s</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle Sidebar for Mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        // Close sidebar
        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        }

        // Handle window resize
        window.addEventListener('resize', function () {
            if (window.innerWidth > 991) {
                closeSidebar();
            }
        });

        // Menu navigation functions
        function showPage(page) {
            // Remove active class from all menu items
            document.querySelectorAll('.sidebar-menu a').forEach(link => {
                link.classList.remove('active');
            });

            // Add active class to clicked menu item
            event.target.closest('a').classList.add('active');

            // Close sidebar on mobile after clicking
            if (window.innerWidth <= 991) {
                closeSidebar();
            }

            // Show alert for demo purposes
            alert(`Navigating to ${page.replace('-', ' ').toUpperCase()} page`);
        }

        // Quick action functions
        function createTicket() {
            alert('Opening Create Ticket form...');
        }

        function searchTickets() {
            alert('Opening Ticket Search...');
        }

        function exportReports() {
            alert('Generating and downloading reports...');
        }

        function manageUsers() {
            alert('Opening User Management...');
        }

        function logout() {
        if (confirm('Are you sure you want to logout?')) {
            document.getElementById('logout-form').submit();
        }
    }

        // Prevent zoom on double tap for iOS
        let lastTouchEnd = 0;
        document.addEventListener('touchend', function (event) {
            const now = (new Date()).getTime();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);

        // Initialize tooltips if needed
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize any Bootstrap tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

    </script>
</body>

</html>
