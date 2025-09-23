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
                    <li><a class="dropdown-item" href="#"><i class="fas fa-exclamation-triangle me-2"></i>Urgent Request</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-check-circle me-2"></i>Ticket Resolved</a></li>
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
                        <a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                       <a class="dropdown-item text-danger" href="#"
   onclick="event.preventDefault(); 
            if(confirm('Apakah Anda yakin ingin logout?')) {
                document.getElementById('logout-form').submit();
            }">
    <i class="fas fa-sign-out-alt me-2"></i> Logout
</a>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
