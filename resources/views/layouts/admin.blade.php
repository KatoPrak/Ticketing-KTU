<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('assets/image/logo-ktu.jpg') }}">
    @vite(['resources/css/admin.css'])
</head>

<body>
    <div class="dashboard-container">
        <!-- SIDEBAR -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>Admin Panel</h3>
                <button class="toggle-btn toggle-sidebar">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="sidebar-menu">
                <a href="{{ route('admin.dashboard') }}" 
                    class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                    data-section="dashboard">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.users.index') }}" 
                    class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" 
                    data-section="users">
                    <i class="fas fa-users"></i>
                    <span>Manage Users</span>
                </a>
                <a href="{{ route('admin.tickets.index') }}" 
                    class="menu-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" 
                    data-section="reports">
                    <i class="fas fa-file-pdf"></i>
                    <span>Reports</span>
                </a>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="navbar">
                <div class="navbar-title">@yield('title', 'Dashboard')</div>

                <div class="navbar-user">
                    <div class="user-info">
                        <span>{{ Auth::user()->name }}</span>
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>

                    <div id="userDropdown" class="user-dropdown">
                        <a class="dropdown-item text-danger" href="#" onclick="confirmLogout(event)">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>

            <div class="content">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    @include('layouts.footer')

    <!-- JS LIBRARIES -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- SWEETALERT2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@vite(['resources/js/admin.js'])
    <!-- PAGE SCRIPTS -->
    @stack('scripts')

    <!-- LOGOUT CONFIRMATION -->
    <script>
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
</body>
</html>
