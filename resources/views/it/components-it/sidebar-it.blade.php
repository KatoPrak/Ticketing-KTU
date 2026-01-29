<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header p-3 border-bottom">
        <h5 class="mb-0">
            <i class="fas fa-tachometer-alt me-2"></i> Dashboard Menu
        </h5>
    </div>

    <nav class="sidebar-menu mt-3">
        <ul class="list-unstyled">
            <li>
                <a href="{{ route('it.dashboard') }}" class="{{ request()->routeIs('it.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home me-2"></i> <span>Dashboard</span>
                </a>
            </li>
            <li>
                {{-- FIX: Tambahkan kondisi agar tidak aktif saat di halaman history --}}
                <a href="{{ route('it.tickets.index') }}"
                    class="{{ (request()->routeIs('it.tickets.*') && !request()->routeIs('it.tickets.history')) ? 'active' : '' }}">
                    <i class="fas fa-ticket-alt me-2"></i> <span>Tickets</span>
                </a>
            </li>
            <li>
                <a href="{{ route('it.tickets.history') }}"
                    class="{{ request()->routeIs('it.tickets.history') ? 'active' : '' }}">
                    <i class="fas fa-history me-2"></i> <span>Ticket History</span>
                </a>
            </li>
            <li>
                <a href="{{ route('it.feedbacks.index') }}"
                    class="{{ request()->routeIs('it.feedbacks.*') ? 'active' : '' }}">
                    <i class="fas fa-comment me-2"></i> <span>Feedbacks</span>
                </a>
            </li>
            <li>
                <a href="{{ route('it.news.index') }}" class="{{ request()->routeIs('it.news.*') ? 'active' : '' }}">
                    <i class="fas fa-newspaper me-2"></i> <span>News</span>
                </a>
            </li>
            <li>
                <a href="{{ route('it.staff.index') }}" class="{{ request()->routeIs('it.staff.*') ? 'active' : '' }}">
                    <i class="fas fa-users me-2"></i> <span>Staff</span>
                </a>
            </li>
            <li class="dropdown">
                <a href="#settingsMenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                    <i class="fas fa-cog"></i>
                    Settings
                </a>
                <ul class="collapse list-unstyled ms-3" id="settingsMenu">
                    <li>
                        <a href="{{ route('it.password.form') }}" class="nav-link">
                            <i class="fas fa-key me-2"></i>Change Password
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); if (confirm('Are you sure you want to log out?')) document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-2 text-danger"></i>Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
    </nav>
</aside>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (sidebar && overlay) {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }
    }

    function closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (sidebar && overlay) {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        }
    }

    // Highlight menu aktif saat diklik
    document.addEventListener("DOMContentLoaded", () => {
        const links = document.querySelectorAll(".sidebar-menu a");
        links.forEach(link => {
            link.addEventListener("click", function () {
                links.forEach(l => l.classList.remove("active"));
                this.classList.add("active");
                closeSidebar(); // tutup otomatis di mobile
            });
        });
    });

    // Toggle sidebar untuk mobile
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (sidebar && overlay) {
            const isOpening = !sidebar.classList.contains('show');

            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');

            // Hanya apply ke mobile
            if (window.innerWidth < 992) {
                if (isOpening) {
                    document.body.classList.add('sidebar-open-mobile');
                } else {
                    document.body.classList.remove('sidebar-open-mobile');
                }
            }
        }
    }

    // Close sidebar
    function closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (sidebar && overlay) {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            document.body.classList.remove('sidebar-open-mobile');
        }
    }

    // Close sidebar ketika klik di overlay
    document.getElementById('sidebarOverlay').addEventListener('click', closeSidebar);

    // Close sidebar ketika klik link di menu (mobile only)
    document.querySelectorAll('.sidebar-menu a').forEach(link => {
        link.addEventListener('click', function () {
            if (window.innerWidth < 992) {
                closeSidebar();
            }
        });
    });

    // Close sidebar dengan Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeSidebar();
        }
    });

    // Handle resize - pastikan state konsisten
    window.addEventListener('resize', function () {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (window.innerWidth >= 992) {
            // Di desktop, pastikan sidebar terbuka dan overlay hilang
            if (sidebar) sidebar.classList.remove('show');
            if (overlay) overlay.classList.remove('show');
            document.body.classList.remove('sidebar-open-mobile');
        }
    });
</script>