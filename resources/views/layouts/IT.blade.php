<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Dashboard - KTU Shipyard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('assets/image/logo-ktu.jpg') }}">
    <style>
        :root {
            --sidebar-width: 250px;
            --navbar-height: 60px;
            --primary-color: #7F56D8;
            --secondary-color: #f8f9fa;
            --text-dark: #333;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: var(--secondary-color);
            font-family: 'Segoe UI', sans-serif;
        }

        .navbar-custom {
            background: linear-gradient(135deg, var(--primary-color), #6366f1);
            height: var(--navbar-height);
        }

        .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.3rem;
        }

        .navbar-nav .nav-link {
            color: white !important;
        }

        .sidebar {
            position: fixed;
            top: var(--navbar-height);
            left: 0;
            width: var(--sidebar-width);
            height: calc(100vh - var(--navbar-height));
            background: #fff;
            border-right: 1px solid #e0e6ed;
            overflow-y: auto;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--text-dark);
            text-decoration: none;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(127, 86, 216, 0.1);
            color: var(--primary-color);
            font-weight: 600;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--navbar-height);
            padding: 25px;
            min-height: calc(100vh - var(--navbar-height));
        }

        @media(max-width:991px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }

    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container-fluid">
            <button class="btn navbar-toggler text-white" type="button" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand" href="#"><i class="fas fa-cogs me-2"></i> IT Support</a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>{{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
        <ul class="sidebar-menu">
            <li><a href="{{ route('it.dashboard') }}" class="active"><i class="fas fa-home me-2"></i> Dashboard</a></li>
            <li><a href="#"><i class="fas fa-ticket-alt me-2"></i> Tickets</a></li>
            <li><a href="#"><i class="fas fa-plus-circle me-2"></i> Create Ticket</a></li>
            <li><a href="#"><i class="fas fa-users me-2"></i> Users</a></li>
            <li><a href="#"><i class="fas fa-chart-bar me-2"></i> Reports</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

    </script>

    @if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('
            success ') }}',
            timer: 2000,
            showConfirmButton: false
        });

    </script>
    @endif
</body>

</html>
