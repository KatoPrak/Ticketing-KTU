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

        <li class="nav-item">
            <a href="#settingsMenu" data-bs-toggle="collapse" class="nav-link d-flex align-items-center {{ Request::is('staff/profile') ? 'active' : 'collapsed' }}" aria-expanded="{{ Request::is('staff/profile') ? 'true' : 'false' }}">
                <i class="fas fa-cog me-2"></i>
                <span>Settings</span>
                <i class="fas fa-chevron-down ms-auto sub-menu-arrow"></i>
            </a>
            <ul id="settingsMenu" class="collapse list-unstyled ps-3 show-if-active {{ Request::is('staff/profile') ? 'show' : '' }}">
                <li class="mt-1">
                    <a href="{{ route('staff.profile') }}" 
                       class="d-flex align-items-center p-2 {{ Request::is('staff/profile') ? 'active' : '' }}" style="font-size: 0.9rem;">
                        <i class="fas fa-user-circle me-2"></i>
                        My Profile
                    </a>
                </li>
                <li class="mt-1">
                    <a href="{{ route('logout') }}" 
                       class="d-flex align-items-center p-2 text-danger" style="font-size: 0.9rem;"
                       onclick="event.preventDefault(); if (confirm('Are you sure you want to log out?')) document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i>
                        Logout
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
        color: #fff !important;
        background-color: #667eea !important;
        border-radius: 5px;
    }
    
    .sidebar-menu .nav-link {
        color: #4b5563;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .sidebar-menu .nav-link:hover {
        background-color: rgba(102, 126, 234, 0.1);
        border-radius: 5px;
    }

    .sub-menu-arrow {
        transition: transform 0.3s ease;
        font-size: 0.8rem;
    }
    
    [aria-expanded="true"] .sub-menu-arrow {
        transform: rotate(180deg);
    }
    
    #settingsMenu a {
        border-left: 2px solid transparent;
        transition: all 0.2s ease;
    }
    
    #settingsMenu a:hover {
        border-left: 2px solid #667eea;
        padding-left: 12px !important;
    }
    
    #settingsMenu a.active {
        border-left: 2px solid #fff;
    }
</style>
