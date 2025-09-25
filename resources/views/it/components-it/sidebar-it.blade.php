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
                <a href="{{ route('it.dashboard') }}" class="{{ request()->routeIs('it.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home me-2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('it.index-ticket') }}" class="{{ request()->routeIs('it.index-ticket') ? 'active' : '' }}">
                    <i class="fas fa-ticket-alt me-2"></i> Tickets
                </a>
            </li>
            <li>
                <a href="{{ route('it.riwayat-ticket') }}" class="{{ request()->routeIs('it.riwayat-ticket') ? 'active' : '' }}">
                    <i class="fas fa-history me-2"></i> Riwayat Tiket
                </a>
            </li>
            <li>
                <a href="#" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fas fa-users me-2"></i> Users
                </a>
            </li>
            <li>
                <a href="#" class="{{ request()->routeIs('assets.*') ? 'active' : '' }}">
                    <i class="fas fa-laptop me-2"></i> Assets
                </a>
            </li>
            <li>
                <a href="#" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar me-2"></i> Reports
                </a>
            </li>
            <li>
                <a href="#" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog me-2"></i> Settings
                </a>
            </li>
            <li>
                <a href="#" class="{{ request()->routeIs('help.*') ? 'active' : '' }}">
                    <i class="fas fa-question-circle me-2"></i> Help & Support
                </a>
            </li>
        </ul>
    </nav>
</aside>
