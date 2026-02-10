<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Staff - Ticketing System')</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="icon" type="image/png" href="{{ asset('assets/image/logo-ktu.jpg') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- ✅ Load via Vite (Hot Reload Support) -->
    @vite([
        'resources/css/user.css', 
        'resources/js/ticket-detail-handler.js', 
        'resources/js/staff.js', 
        'resources/js/user.js'
    ])

    @yield('styles')
</head>
<body class="d-flex flex-column min-vh-100 @yield('body-class')">
    <!-- Overlay Sidebar -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- Navbar -->
    @include('staff.partials.navbar')

    <div class="container-fluid flex-grow-1">
        <div class="row h-100">
            <!-- Sidebar -->
            <aside class="col-md-3 col-lg-2 p-0">
                @include('staff.partials.sidebar')
            </aside>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 px-4 py-3 main-content">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Footer -->
    @include('layouts.footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>