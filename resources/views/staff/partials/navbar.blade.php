{{-- Navbar Component --}}
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        {{-- Mobile Sidebar Toggle --}}
        <button class="btn navbar-toggler d-lg-none me-3" type="button" id="sidebarToggler">
            <i class="fas fa-bars"></i>
        </button>
        <a class="navbar-brand" href="{{ route('staff.dashboard') }}">
            <img src="{{ asset('assets/image/logo-ktu.jpg') }}" alt="KTU Logo" class="me-2"
                style="height: 35px; width: auto; object-fit: contain;">
            IT Ticketing System
        </a>

        {{-- Navbar Right --}}
        <div class="navbar-nav ms-auto d-flex flex-row align-items-center">

            {{-- User Profile Dropdown --}}
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle me-2" style="font-size: 28px;"></i>
                    <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    {{-- Change Password --}}
                    <li>
                        <a class="dropdown-item" href="{{ route('password.form') }}">
                            <i class="fas fa-key me-2"></i>Change Password
                        </a>
                    </li>
                    
                    <li><hr class="dropdown-divider"></li>
                    
                    {{-- Help --}}
                    <li>
                        <a class="dropdown-item" href="#" onclick="showHelp(); return false;">
                            <i class="fas fa-question-circle me-2"></i>Help & Support
                        </a>
                    </li>
                    
                    {{-- Logout --}}
                    <li>
                        <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); if (confirm('Are you sure you want to logout?')) { document.getElementById('logout-form').submit(); }">
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

{{-- Sidebar Overlay (for mobile) --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sidebar Toggle for Mobile
        const sidebarToggler = document.getElementById('sidebarToggler');
        const sidebar = document.querySelector('.sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        if (sidebarToggler) {
            sidebarToggler.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
            });
        }

        // Close sidebar when overlay is clicked
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });
        }

        // Help Function
        window.showHelp = function() {
            const helpMessage = `
IT Support Contact Information:

📧 Email: it@ktushipyard.com
📱 WhatsApp: +62-813-7099-9910
🕒 Working Hours: Mon-Sat, 08:00 - 16:00

For urgent issues, please call our hotline.
            `.trim();
            
            alert(helpMessage);
        };

        // Active Link Highlighting
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.navbar-nav a.nav-link, .navbar-nav a.dropdown-item');

        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && href !== '#' && currentPath.includes(href)) {
                link.classList.add('active');
            }
        });

        // Manual Dropdown Toggle (Bootstrap alternative)
        const dropdownToggles = document.querySelectorAll('[data-bs-toggle="dropdown"]');
        
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const dropdownMenu = this.nextElementSibling;
                const isActive = dropdownMenu.classList.contains('show');
                
                // Close all other dropdowns
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    menu.classList.remove('show');
                });
                
                // Toggle current dropdown
                if (!isActive) {
                    dropdownMenu.classList.add('show');
                }
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });
    });
</script>

<style>
    /* Force navbar to be fixed */
    .navbar-custom {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        z-index: 1030 !important;
        background: linear-gradient(135deg, var(--primary-color), #6366f1) !important;
        height: var(--navbar-height);
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    }

    /* Fix dropdown z-index */
    .navbar-nav .dropdown {
        position: static;
    }

    .navbar-nav .dropdown-menu {
        position: absolute !important;
        z-index: 9999 !important;
        top: 100% !important;
        right: 10px !important;
        left: auto !important;
        margin-top: 0.5rem;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
    }

    .navbar-nav .dropdown-menu.show {
        display: block !important;
    }

    /* Additional navbar styles */
    .navbar-nav .nav-link.active {
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 6px;
    }

    .dropdown-item.active,
    .dropdown-item:active {
        background-color: rgba(79, 70, 229, 0.1);
        color: var(--primary-color);
    }

    /* Smooth transitions */
    .sidebar,
    .sidebar-overlay {
        transition: all 0.3s ease;
    }
/* Navbar Brand & Logo */
.navbar-custom .navbar-brand {
    display: flex;
    align-items: center;
    font-weight: 700;          /* Bold teks */
    color: #ffffff !important; /* Warna putih */
    font-size: 1.5rem;         /* Lebih besar */
}

.navbar-custom .navbar-brand img {
    height: 45px;              /* Logo lebih besar */
    width: auto;
    object-fit: contain;
    margin-right: 0.75rem;     /* Spasi antara logo dan teks */
}

/* Navbar Links */
.navbar-nav .nav-link {
    color: #ffffff !important;
    font-weight: 700;           /* Bold */
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
}

.navbar-nav .nav-link i {
    color: #ffffff !important; /* Icon putih */
    font-size: 1.2rem;
    margin-right: 0.3rem;
}

/* Dropdown items */
.navbar-nav .dropdown-item {
    color: #000000 !important; /* Putih */
    font-weight: 700;           /* Bold */
    display: flex;
    align-items: center;
}

.navbar-nav .dropdown-item i {
    color: #000000 !important;
    margin-right: 0.5rem;
}

/* Dropdown hover */
.navbar-nav .dropdown-item:hover,
.navbar-nav .dropdown-item.active {
    background-color: rgba(255, 255, 255, 0.15) !important;
    color: #000000 !important;
}

    /* Fix dropdown positioning on mobile */
    @media (max-width: 768px) {
        .navbar-nav .dropdown-menu {
            position: absolute !important;
            right: 5px;
            left: auto;
            margin-top: 8px;
        }
    }

    /* Ensure body has padding for fixed navbar */
    body {
        padding-top: var(--navbar-height) !important;
    }
</style>