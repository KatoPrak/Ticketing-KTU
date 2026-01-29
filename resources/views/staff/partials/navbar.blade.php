{{-- Navbar Component --}}
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        {{-- Mobile Sidebar Toggle --}}
        <button class="btn navbar-toggler d-lg-none me-3" type="button" id="sidebarToggler">
            <i class="fas fa-bars"></i>
        </button>
        <a class="navbar-brand" href="{{ route('staff.dashboard') }}">
            <img src="{{ asset('assets/image/logo-ktu.jpg') }}" alt="KTU Logo" class="me-2 navbar-logo">
            <span class="navbar-title">IT Support Ticketing System</span>
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
IT Support Contact Information

📧 Email: ferdinal.sukman@ktushipyard.com
📱 WhatsApp: +62-813-7099-9910
🕒 Working Hours:
   • Monday–Friday : 08:00 – 16:00
   • Saturday            : 08:00 – 14:00

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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

    /* ========================================
       NAVBAR BRAND & LOGO - RESPONSIVE
    ======================================== */
    .navbar-custom .navbar-brand {
        display: flex;
        align-items: center;
        font-weight: 600;
        color: #ffffff !important;
        font-size: 1.1rem; /* DIKECILKAN dari 1.5rem */
        transition: font-size 0.3s ease;
    }

    .navbar-logo {
        height: 45px;
        width: auto;
        object-fit: contain;
        margin-right: 0.75rem;
        transition: height 0.3s ease;
    }

    .navbar-title {
        white-space: nowrap;
        transition: font-size 0.3s ease;
        font-weight: 500; /* Slightly lighter weight */
    }

    /* Navbar Links */
    .navbar-nav .nav-link {
        color: #ffffff !important;
        font-weight: 700;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
    }

    .navbar-nav .nav-link i {
        color: #ffffff !important;
        font-size: 1.2rem;
        margin-right: 0.3rem;
    }

    /* Dropdown items */
    .navbar-nav .dropdown-item {
        color: #000000 !important;
        font-weight: 700;
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

    /* ========================================
       RESPONSIVE BREAKPOINTS
    ======================================== */
    
    /* Large tablets and small desktops (992px - 1200px) */
    @media (max-width: 1200px) {
        .navbar-custom .navbar-brand {
            font-size: 1rem; /* DIKECILKAN dari 1.3rem */
        }
        
        .navbar-logo {
            height: 40px;
        }
    }

    /* Tablets (768px - 992px) */
    @media (max-width: 992px) {
        .navbar-custom .navbar-brand {
            font-size: 0.9rem; /* DIKECILKAN dari 1.1rem */
        }
        
        .navbar-logo {
            height: 35px;
        }
    }

    /* Large phones and small tablets (576px - 768px) */
    @media (max-width: 768px) {
        .navbar-custom .navbar-brand {
            font-size: 0.8rem; /* DIKECILKAN dari 0.95rem */
        }
        
        .navbar-logo {
            height: 32px;
            margin-right: 0.5rem;
        }
        
        .navbar-nav .dropdown-menu {
            position: absolute !important;
            right: 5px;
            left: auto;
            margin-top: 8px;
        }
    }

    /* Small phones (up to 576px) */
    @media (max-width: 576px) {
        .navbar-custom .navbar-brand {
            font-size: 0.7rem; /* DIKECILKAN dari 0.85rem */
        }
        
        .navbar-logo {
            height: 28px;
            margin-right: 0.4rem;
        }
    }

    /* Extra small phones (up to 400px) */
    @media (max-width: 400px) {
        .navbar-custom .navbar-brand {
            font-size: 0.65rem; /* DIKECILKAN dari 0.75rem */
        }
        
        .navbar-logo {
            height: 25px;
            margin-right: 0.3rem;
        }
        
        .navbar-title {
            /* Optionally truncate very long text */
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    }

    /* iPhone SE and similar (320px - 375px) */
    @media (max-width: 375px) {
        .navbar-custom .navbar-brand {
            font-size: 0.6rem; /* DIKECILKAN dari 0.7rem */
        }
        
        .navbar-logo {
            height: 22px;
            margin-right: 0.25rem;
        }
        
        .navbar-title {
            max-width: 120px;
        }
    }

    /* Landscape orientation for small devices */
    @media (max-height: 500px) and (orientation: landscape) {
        .navbar-custom {
            height: 50px;
        }
        
        .navbar-custom .navbar-brand {
            font-size: 0.75rem; /* DIKECILKAN dari 0.85rem */
        }
        
        .navbar-logo {
            height: 28px;
        }
    }

    /* Ensure body has padding for fixed navbar */
    body {
        padding-top: var(--navbar-height) !important;
    }

    /* ========================================
       iOS SPECIFIC FIXES
    ======================================== */
    @supports (-webkit-touch-callout: none) {
        /* iOS Safari specific */
        .navbar-custom .navbar-brand {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    }
</style>