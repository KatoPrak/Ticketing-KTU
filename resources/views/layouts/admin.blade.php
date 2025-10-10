<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - @yield('title', 'Dashboard')</title> {{-- Default title is Dashboard --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('assets/image/logo-ktu.jpg') }}">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>

<body>
    <div class="dashboard-container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>Admin Panel</h3>
                <button class="toggle-btn toggle-sidebar">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="sidebar-menu">
                {{-- Links can be updated to use route() helper for active states --}}
                <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-section="dashboard">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" data-section="users">
                    <i class="fas fa-users"></i>
                    <span>Manage Users</span>
                </a>
                <a href="{{ route('admin.tickets.index') }}" class="menu-item {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}" data-section="tickets">
                    <i class="fas fa-ticket-alt"></i>
                    <span>Tickets</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="menu-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" data-section="reports">
                    <i class="fas fa-file-pdf"></i>
                    <span>Reports</span>
                </a>
            </div>
        </div>

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
                        <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
<!-- 👇 TAMBAHKAN FOOTER DI SINI -->
    @include('layouts.footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

    {{-- Page-specific scripts can be pushed here --}}
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>
</html>
