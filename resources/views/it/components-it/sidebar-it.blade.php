<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header p-3 border-bottom">
        <h5 class="mb-0">
            <i class="fas fa-tachometer-alt me-2"></i> Dashboard Menu
        </h5>
    </div>

    <nav class="sidebar-menu mt-3">
        <ul class="list-unstyled">
            <li>
                <a href="{{ route('it.dashboard') }}" 
                   class="{{ request()->routeIs('it.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home me-2"></i> <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('it.index-ticket') }}" 
                   class="{{ request()->routeIs('it.index-ticket') ? 'active' : '' }}">
                    <i class="fas fa-ticket-alt me-2"></i> <span>Tickets</span>
                </a>
            </li>
            <li>
                <a href="{{ route('it.riwayat-ticket') }}" 
                   class="{{ request()->routeIs('it.riwayat-ticket') ? 'active' : '' }}">
                    <i class="fas fa-history me-2"></i> <span>Ticket History</span>
                </a>
            </li>
            <li>
                <a href="{{ route('it.news.index') }}" 
                   class="{{ request()->routeIs('it.news.index') ? 'active' : '' }}">
                    <i class="fas fa-newspaper me-2"></i> <span>News</span>
                </a>
            </li>
            <li>
                <a href="{{ route('it.staff.index') }}" 
                   class="{{ request()->routeIs('it.staff.*') ? 'active' : '' }}">
                    <i class="fas fa-users me-2"></i> <span>Staff</span>
                </a>
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
            link.addEventListener("click", function() {
                links.forEach(l => l.classList.remove("active"));
                this.classList.add("active");
                closeSidebar(); // tutup otomatis di mobile
            });
        });
    });
</script>
