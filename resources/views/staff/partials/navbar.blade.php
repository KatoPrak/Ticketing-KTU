<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <!-- Mobile Sidebar Toggle -->
        <button class="btn navbar-toggler d-lg-none me-3" type="button" id="sidebarToggler">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Brand -->
        <a class="navbar-brand" href="#">
            <img src="{{ asset('assets/image/logo-ktu.jpg') }}" alt="KTU Logo" class="me-2"
                style="height: 35px; width: auto; object-fit: contain;">
            IT Ticketing System
        </a>

        <!-- Navbar Right -->
        <div class="navbar-nav ms-auto d-flex flex-row align-items-center">
            <!-- User Profile -->
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle me-2" style="font-size: 28px;"></i>
                    <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
    <li class="px-3 py-2 border-bottom">
        <div>
            <strong>{{ Auth::user()->name }}</strong><br>
            <small class="text-muted">{{ Auth::user()->email }}</small>
        </div>
    </li>
    <li>
        <a class="dropdown-item" href="{{ route('password.form') }}">
            <i class="fas fa-key me-2"></i>Change Password
        </a>
    </li>
    <li>
        <a class="dropdown-item" href="#" onclick="showHelp()">
            <i class="fas fa-question-circle me-2"></i>Help
        </a>
    </li>
    <li>
        <a class="dropdown-item text-danger" href="{{ route('logout') }}"
           onclick="event.preventDefault(); if (confirm('Are you sure you want to logout?')) { document.getElementById('logout-form').submit(); }">
            <i class="fas fa-sign-out-alt me-2"></i>Logout
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

<script>
    // Help info
    function showHelp() {
        alert('Please contact the IT team for assistance.\n\nEmail: it@ktushipyard.com\nPhone: +62-813-7099-9910');
    }

    // Active link handler
    document.addEventListener("DOMContentLoaded", () => {
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll(".navbar-nav a.nav-link, .navbar-nav a.dropdown-item");

        navLinks.forEach(link => {
            const href = link.getAttribute("href");
            if (href && currentPath.startsWith(href)) {
                link.classList.add("active-link");
            }
        });
    });
</script>

<style>
    .nav-link.active-link {
        color: #fff !important;
        background-color: #0d6efd;
        border-radius: 6px;
        padding: 6px 10px;
    }
</style>
