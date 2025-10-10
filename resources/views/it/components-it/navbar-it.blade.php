<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
    <div class="container-fluid">
        <!-- Mobile Sidebar Toggle -->
        <button class="btn navbar-toggler d-lg-none me-2" type="button" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="{{ asset('assets/image/ktu-shadow.png') }}" alt="KTU Logo" class="me-2"
                style="height: 35px; width: auto; object-fit: contain;">
            <span class="fw-bold">IT Support</span>
        </a>

        <!-- Navbar Right -->
        <div class="navbar-nav ms-auto">
            <!-- User Profile -->
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle me-1"></i>
                    <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
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

<script>
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    if (!sidebar || !overlay) return;

    sidebar.classList.toggle('show');
    overlay.classList.toggle('show');
}
</script>
