{{-- Navbar Component --}}
<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
    <div class="container-fluid">
        {{-- Mobile Sidebar Toggle --}}
        <button class="btn navbar-toggler d-lg-none me-2" type="button" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>

        {{-- Brand --}}
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="{{ asset('assets/image/ktu-shadow.png') }}" alt="KTU Logo" class="me-2 flex-shrink-0"
                style="height: 35px; width: auto; object-fit: contain;">
            <span class="fw-bold navbar-brand-text text-truncate">IT Support Ticketing System</span>
        </a>

        {{-- Navbar Right --}}
        <div class="navbar-nav ms-auto">
            {{-- User Profile Dropdown --}}
            <div class="nav-item dropdown">
                <button class="nav-link dropdown-toggle d-flex align-items-center border-0 bg-transparent p-0" id="userDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer; z-index: 1105; color: white;">
                    <i class="fas fa-user-circle me-1" style="font-size: 1.2rem;"></i>
                    <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userDropdown" style="z-index: 9999;">
                    <li class="dropdown-header">
                        <div class="d-flex flex-column">
                            <span class="fw-bold">{{ Auth::user()->name }}</span>
                            <small class="text-muted">{{ Auth::user()->email }}</small>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('it.profile') }}">
                            <i class="fas fa-user-circle me-2"></i>My Profile
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="#"
                            onclick="handleLogout(event)">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

{{-- Sidebar Overlay for Mobile --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<script>
// Sidebar Toggle Functions
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar') || document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (sidebar) sidebar.classList.toggle('show');
    if (overlay) overlay.classList.toggle('show');
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar') || document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (sidebar) sidebar.classList.remove('show');
    if (overlay) overlay.classList.remove('show');
}

// Close sidebar with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeSidebar();
    }
});

// Handle Logout
function handleLogout(event) {
    event.preventDefault();
    
    if (confirm('Are you sure you want to log out?')) {
        document.getElementById('logout-form').submit();
    }
}

// Make functions available globally
window.toggleSidebar = toggleSidebar;
window.closeSidebar = closeSidebar;
window.handleLogout = handleLogout;
</script>

<style>
/* Navbar Base Styles */
.navbar-custom {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    height: var(--navbar-height, 60px);
    padding: 0.75rem 1rem;
    z-index: 1030;
}

.navbar-custom .navbar-brand {
    color: #ffffff !important;
    font-weight: 700;
    font-size: 1.3rem;
    flex: 1;
    min-width: 0;
    max-width: calc(100% - 150px);
}

/* Responsive Brand Text */
.navbar-brand-text {
    font-size: clamp(0.85rem, 2.5vw, 1.3rem);
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.navbar-custom .navbar-toggler {
    border: none;
    color: #ffffff;
    background: rgba(255, 255, 255, 0.1);
    padding: 0.4rem 0.6rem;
    border-radius: 6px;
}

.navbar-custom .navbar-toggler:hover {
    background: rgba(255, 255, 255, 0.2);
}

/* Navbar Links */
.navbar-nav .nav-link {
    color: #ffffff !important;
    font-weight: 500;
    padding: 0.5rem 1rem;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    border-radius: 6px;
    cursor: pointer;
}

.navbar-nav .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.15);
}

.navbar-nav .nav-link i {
    font-size: 1.3rem;
}

/* Dropdown Styles */
.navbar-nav .dropdown {
    position: relative;
}

/* Standard Bootstrap Dropdown - removing custom transitions that might be causing visibility issues */
.navbar-nav .dropdown-menu {
    display: none;
    position: absolute !important;
    top: 100% !important;
    right: 0 !important;
    left: auto !important;
    background: #ffffff !important;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    min-width: 250px;
    padding: 0.5rem 0;
    margin: 0.5rem 0 0 0 !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
    z-index: 9999 !important;
}

.navbar-nav .dropdown-menu.show {
    display: block !important;
}

.navbar-nav .dropdown-header {
    padding: 0.75rem 1rem;
    color: var(--dark-color, #1f2937);
    font-weight: 600;
}

.navbar-nav .dropdown-item {
    color: var(--dark-color, #1f2937) !important;
    font-weight: 500;
    padding: 0.6rem 1rem;
    display: flex;
    align-items: center;
    transition: all 0.2s ease;
    cursor: pointer;
}

.navbar-nav .dropdown-item i {
    width: 20px;
    text-align: center;
    color: var(--light-gray, #64748b);
}

.navbar-nav .dropdown-item:hover {
    background-color: rgba(79, 70, 229, 0.08) !important;
    color: var(--primary-color, #4f46e5) !important;
}

.navbar-nav .dropdown-item:hover i {
    color: var(--primary-color, #4f46e5) !important;
}

.navbar-nav .dropdown-item.text-danger {
    color: var(--danger-color, #ef4444) !important;
}

.navbar-nav .dropdown-item.text-danger:hover {
    background-color: rgba(239, 68, 68, 0.08) !important;
    color: var(--danger-color, #ef4444) !important;
}

.navbar-nav .dropdown-item.text-danger:hover i {
    color: var(--danger-color, #ef4444) !important;
}

.navbar-nav .dropdown-divider {
    margin: 0.5rem 0;
    border-color: #e5e7eb;
}

/* Sidebar Overlay */
.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1020;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.sidebar-overlay.show {
    opacity: 1;
    visibility: visible;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .navbar-custom {
        padding: 0.5rem 0.75rem;
    }

    .navbar-custom .navbar-brand {
        font-size: 1.1rem;
        max-width: calc(100% - 120px);
    }

    .navbar-brand-text {
        font-size: clamp(0.75rem, 2vw, 1rem);
    }

    .navbar-custom .navbar-brand img {
        height: 30px !important;
    }

    .navbar-nav .dropdown-menu {
        position: fixed !important;
        top: var(--navbar-height, 60px) !important;
        right: 10px !important;
        min-width: 220px;
        max-width: calc(100vw - 20px);
    }

    .navbar-nav .nav-link span {
        font-size: 0.9rem;
    }
}

@media (max-width: 576px) {
    .navbar-custom .navbar-brand {
        max-width: calc(100% - 100px);
    }

    .navbar-brand-text {
        font-size: clamp(0.7rem, 1.8vw, 0.85rem);
    }

    .navbar-nav .dropdown-menu {
        right: 5px !important;
        min-width: 200px;
    }

    .navbar-nav .dropdown-item {
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
    }
}

@media (max-width: 400px) {
    .navbar-custom .navbar-brand {
        max-width: calc(100% - 90px);
    }

    .navbar-brand-text {
        font-size: 0.65rem;
    }
    
    .navbar-custom .navbar-brand img {
        height: 25px !important;
    }
}

/* Body Padding for Fixed Navbar */
body {
    padding-top: var(--navbar-height, 60px) !important;
}
</style>