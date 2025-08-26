<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>IT Dashboard - KTU Shipyard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-width: 250px;
            --navbar-height: 56px;
            --primary-color: #7F56D8;
            --secondary-color: #f8f9fa;
            --text-dark: #333;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--secondary-color);
            overflow-x: hidden;
        }

        /* Navbar Styles */
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary-color), #6366f1);
            height: var(--navbar-height);
            padding: 0.5rem 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
        }

        .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.2rem;
            padding: 0;
        }

        .navbar-nav .nav-link {
            color: white !important;
            font-weight: 500;
            padding: 0.3rem 0.8rem;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }

        .navbar-toggler {
            border: none;
            color: white;
            padding: 0.25rem 0.5rem;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: var(--navbar-height);
            left: -var(--sidebar-width);
            width: var(--sidebar-width);
            height: calc(100vh - var(--navbar-height));
            background: linear-gradient(180deg, #ffffff, #f8f9fa);
            border-right: 1px solid #e0e6ed;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            transition: left 0.3s ease;
            z-index: 1025;
        }

        .sidebar.show {
            left: 0;
        }

        .sidebar-header {
            padding: 1rem;
            border-bottom: 1px solid #e0e6ed;
            background: white;
        }

        .sidebar-header h5 {
            color: var(--text-dark);
            margin: 0;
            font-weight: 600;
            font-size: 1rem;
        }

        .sidebar-menu {
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .sidebar-menu li {
            margin: 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--text-dark);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            font-size: 0.9rem;
        }

        .sidebar-menu a:hover {
            background: linear-gradient(90deg, rgba(127, 86, 216, 0.1), transparent);
            border-left-color: var(--primary-color);
            color: var(--primary-color);
        }

        .sidebar-menu a.active {
            background: linear-gradient(90deg, rgba(127, 86, 216, 0.15), transparent);
            border-left-color: var(--primary-color);
            color: var(--primary-color);
            font-weight: 600;
        }

        .sidebar-menu i {
            width: 18px;
            margin-right: 10px;
            font-size: 1rem;
        }

        /* Sidebar Overlay for Mobile */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1020;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* Main Content Styles */
        .main-content {
            margin-top: var(--navbar-height);
            padding: 1rem;
            min-height: calc(100vh - var(--navbar-height));
            transition: all 0.3s ease;
        }

        .welcome-card {
            background: linear-gradient(135deg, var(--primary-color), #6366f1);
            color: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 25px rgba(127, 86, 216, 0.25);
        }

        .welcome-card h2 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .welcome-card p {
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }

        .welcome-card small {
            font-size: 0.8rem;
        }

        .dashboard-card {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.25rem;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .stat-card {
            text-align: center;
            padding: 1rem;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .stat-label {
            color: #666;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        /* Table Responsive */
        .table-responsive {
            border-radius: 8px;
        }

        .table {
            font-size: 0.9rem;
        }

        .table th {
            font-size: 0.8rem;
            font-weight: 600;
            color: #666;
            border-top: none;
        }

        /* Button Styles */
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-color), #6366f1);
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(127, 86, 216, 0.4);
        }

        .btn-outline-primary,
        .btn-outline-secondary,
        .btn-outline-info {
            border-radius: 8px;
            font-size: 0.9rem;
            padding: 0.6rem 1rem;
        }

        /* Dropdown Styles */
        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            font-size: 0.9rem;
        }

        /* Badge Styles */
        .badge {
            font-size: 0.75rem;
        }

        /* Desktop Styles */
        @media (min-width: 992px) {
            .sidebar {
                left: 0;
            }
            
            .main-content {
                margin-left: var(--sidebar-width);
                padding: 2rem;
            }
            
            .welcome-card {
                padding: 2rem;
            }
            
            .dashboard-card {
                padding: 1.5rem;
            }
            
            .stat-number {
                font-size: 2.5rem;
            }
            
            .welcome-card h2 {
                font-size: 2rem;
            }
        }

        /* Tablet Styles */
        @media (min-width: 768px) and (max-width: 991px) {
            .main-content {
                padding: 1.5rem;
            }
            
            .welcome-card {
                padding: 1.75rem;
            }
            
            .stat-number {
                font-size: 2.2rem;
            }
        }

        /* Mobile Styles */
        @media (max-width: 767px) {
            .navbar-brand {
                font-size: 1rem;
            }
            
            .main-content {
                padding: 0.75rem;
            }
            
            .welcome-card {
                padding: 1rem;
                margin-bottom: 1rem;
                text-align: center;
            }
            
            .welcome-card .row {
                text-align: center;
            }
            
            .welcome-card h2 {
                font-size: 1.3rem;
            }
            
            .welcome-card p {
                font-size: 0.9rem;
            }
            
            .stat-number {
                font-size: 1.8rem;
            }
            
            .stat-label {
                font-size: 0.75rem;
            }
            
            .dashboard-card {
                padding: 1rem;
                margin-bottom: 1rem;
            }
            
            .table {
                font-size: 0.8rem;
            }
            
            .table th,
            .table td {
                padding: 0.5rem 0.25rem;
            }
            
            .btn-primary-custom,
            .btn-outline-primary,
            .btn-outline-secondary,
            .btn-outline-info {
                font-size: 0.85rem;
                padding: 0.5rem 0.75rem;
            }
            
            .d-grid {
                gap: 0.75rem !important;
            }
            
            /* Hide some table columns on very small screens */
            .table td:nth-child(5),
            .table th:nth-child(5) {
                display: none;
            }
        }

        @media (max-width: 575px) {
            .navbar-brand {
                font-size: 0.9rem;
            }
            
            .main-content {
                padding: 0.5rem;
            }
            
            .welcome-card h2 {
                font-size: 1.2rem;
            }
            
            .stat-number {
                font-size: 1.6rem;
            }
            
            /* Hide more table columns on extra small screens */
            .table td:nth-child(4),
            .table th:nth-child(4),
            .table td:nth-child(5),
            .table th:nth-child(5) {
                display: none;
            }
            
            .dropdown-menu {
                font-size: 0.8rem;
            }
        }

        /* Fix for horizontal scrolling */
        .container-fluid {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        @media (min-width: 576px) {
            .container-fluid {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
    </style>
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
            <a class="navbar-brand" href="#">
                <i class="fas fa-cogs me-2"></i>
                IT Support System
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
                        <li><a class="dropdown-item" href="#"><i class="fas fa-exclamation-triangle me-2"></i>Urgent Request</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-check-circle me-2"></i>Ticket Resolved</a></li>
                    </ul>
                </div>

                <!-- User Profile -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        <span class="d-none d-sm-inline">Admin User</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="#" onclick="logout()">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
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
                    <p class="mb-0">Admin User (ID-001)</p>
                    <small class="opacity-75">IT Support Ticketing System - KTU Shipyard</small>
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
        window.addEventListener('resize', function() {
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
                alert('Logging out...');
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
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize any Bootstrap tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>

</html>