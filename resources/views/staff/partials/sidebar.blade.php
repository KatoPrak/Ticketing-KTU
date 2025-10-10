<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h5><i class="fas fa-tachometer-alt me-2"></i>Main Menu</h5>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('staff.dashboard') }}" 
               class="{{ Request::is('staff/dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('staff.tickets.index') }}" 
               class="{{ Request::is('staff/tickets') ? 'active' : '' }}">
                <i class="fas fa-ticket-alt"></i>
                My Tickets
            </a>
        </li>

        <!-- 🔧 Tambahan Menu Pengaturan -->
        <li class="dropdown">
            <a href="#settingsMenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-cog"></i>
                Pengaturan
            </a>
            <ul class="collapse list-unstyled ms-3" id="settingsMenu">
                <li>
                    <a href="{{ route('password.form') }}">
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
        </li>
    </ul>
</div>


<style>
    /* Tambahkan warna aktif sederhana tanpa ubah layout */
    .sidebar-menu a.active {
        color: #fff;
        background-color: #0d6efd;
        border-radius: 5px;
    }
</style>
