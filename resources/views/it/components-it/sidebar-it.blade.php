<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h5><i class="fas fa-tachometer-alt me-2"></i>Dashboard Menu</h5>
    </div>

    <ul class="sidebar-menu">
        <li><a href="#" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="#"><i class="fas fa-ticket-alt"></i> Tickets</a></li>
        
        <li>
            <a href="{{ route('it.riwayat-ticket') }}">
                <i class="fas fa-history"></i>
                Riwayat Tiket
            </a>
        </li>
        <li><a href="#" onclick="showPage('users')"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="#" onclick="showPage('assets')"><i class="fas fa-laptop"></i> Assets</a></li>
        <li><a href="#" onclick="showPage('reports')"><i class="fas fa-chart-bar"></i> Reports</a></li>
        <li><a href="#" onclick="showPage('maintenance')"><i class="fas fa-tools"></i> Maintenance</a></li>
        <li><a href="#" onclick="showPage('settings')"><i class="fas fa-cog"></i> Settings</a></li>
        <li><a href="#" onclick="showPage('help')"><i class="fas fa-question-circle"></i> Help & Support</a></li>
    </ul>
</div>
