{{-- resources/views/layouts/navbar.blade.php --}}
<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
    <div class="container-fluid">
        {{-- Mobile Sidebar Toggle --}}
        <button class="btn navbar-toggler d-lg-none me-3" type="button" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>

        {{-- Brand --}}
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="{{ asset('assets/image/ktu-shadow.png') }}" alt="KTU Logo" class="me-2"
                 style="height: 35px; width: auto; object-fit: contain;">
            IT Ticketing System
        </a>

        {{-- Navbar Right --}}
        <div class="navbar-nav ms-auto d-flex flex-row align-items-center">
            {{-- User Profile Dropdown --}}
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                   id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle me-2" style="font-size: 28px;"></i>
                    <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userDropdown">
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
                        <a class="dropdown-item" href="#" id="helpBtn">
                            <i class="fas fa-question-circle me-2"></i>Help
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item text-danger" href="#" id="logoutBtn">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

{{-- Hidden Logout Form --}}
<form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display:none;">
    @csrf
</form>

{{-- Sidebar Overlay --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- Include JS --}}
@vite(['resources/js/it.js'])

{{-- Additional JS --}}
<script>
document.addEventListener("DOMContentLoaded", () => {
    // Sidebar toggle
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        document.querySelector('.sidebar')?.classList.toggle('show');
        document.getElementById('sidebarOverlay')?.classList.toggle('show');
    });

    // Help button
    document.getElementById('helpBtn')?.addEventListener('click', () => {
        alert('Please contact the IT team for assistance.\n\nEmail: it@ktushipyard.com\nWhatsApp: +62-813-7099-9910');
    });

    // Logout button
    document.getElementById('logoutBtn')?.addEventListener('click', (e) => {
        e.preventDefault();
        if(confirm('Are you sure you want to logout?')) {
            document.getElementById('logoutForm').submit();
        }
    });
});
</script>
