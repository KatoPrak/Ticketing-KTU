<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('assets/image/logo-ktu.jpg') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom Admin CSS -->
    @vite('resources/css/admin.css')
    
    <!-- Stack Styles dari Child Views -->
    @stack('styles')
</head>
<body>
    <div class="dashboard-container">
        <!-- SIDEBAR - ✅ HAPUS SEMUA data-section -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>Admin Panel</h3>
                <button class="toggle-btn" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="sidebar-menu">
                <!-- ✅ Dashboard - NO data-section -->
                <a href="{{ route('admin.dashboard') }}" 
                   class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>

                <!-- ✅ Manage Users - NO data-section -->
                <a href="{{ route('admin.users.index') }}" 
                   class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Manage Users</span>
                </a>

                <!-- ✅ Reports - NO data-section, FIX routeIs -->
                <a href="{{ route('admin.tickets.index') }}" 
                   class="menu-item {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}">
                    <i class="fas fa-file-pdf"></i>
                    <span>Reports</span>
                <!-- ✅ Locations - NO data-section -->
                <a href="{{ route('admin.locations.index') }}" 
                   class="menu-item {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Locations</span>
                </a>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main-content">
            <!-- NAVBAR with Mobile Toggle Button -->
            <div class="navbar">
                <!-- ✅ Mobile Hamburger Button -->
                <button class="btn toggle-btn d-md-none" onclick="toggleSidebar()" 
                        style="background: none; border: none; padding: 5px 10px; margin-right: 10px;">
                    <i class="fas fa-bars" style="font-size: 20px; color: #2c3e50;"></i>
                </button>
                
                <div class="navbar-title">@yield('title', 'Dashboard')</div>

<div class="navbar-user">
    <div class="dropdown">
        <button class="user-info border-0 bg-transparent p-0" id="userDropdownToggle" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
            <span>{{ Auth::user()->name }}</span>
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>
        </button>

        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdownToggle">
            <li>
                <a class="dropdown-item text-danger" href="#" onclick="confirmLogout(event)">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </li>
        </ul>
    </div>
    
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>
            </div>

            <!-- CONTENT -->
            <div class="content">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- ✅ Sidebar Overlay untuk Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- FOOTER -->
    @include('layouts.footer')

    <!-- JS LIBRARIES -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- ✅ HANYA LOAD 1 FILE JS ADMIN -->
    @vite('resources/js/admin.js')

    <!-- ✅ CORE JAVASCRIPT FUNCTIONS -->
    <script>
        // Toggle Sidebar (Mobile)
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        // Close Sidebar
        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        }

        // Close sidebar on window resize to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        });

        // Logout Confirmation
        function confirmLogout(event) {
            event.preventDefault();

            Swal.fire({
                title: "Log Out?",
                text: "Are you sure you want to log out of this account?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, Log Out",
                cancelButtonText: "Cancel",
                reverseButtons: true,
                backdrop: true,
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }
    </script>

    <!-- Stack Scripts dari Child Views -->
    @stack('scripts')
</body>
</html>