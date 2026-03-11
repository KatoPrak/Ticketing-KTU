{{-- Navbar Component --}}
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        {{-- Mobile Sidebar Toggle --}}
        <button class="btn navbar-toggler d-lg-none me-3" type="button" id="sidebarToggler">
            <i class="fas fa-bars"></i>
        </button>
        {{-- Desktop Sidebar Toggle --}}
        <button class="btn text-white d-none d-lg-inline-flex align-items-center me-2" type="button" id="desktopSidebarToggler" title="Toggle Sidebar">
            <i class="fas fa-bars" id="desktopToggleIcon"></i>
        </button>
        <a class="navbar-brand" href="{{ route('staff.dashboard') }}">
            <div class="navbar-logo-wrapper">
                <img src="{{ asset('assets/image/logo-ktu.jpg') }}" alt="KTU Logo" class="navbar-logo">
            </div>
            <span class="navbar-title">IT Support Ticketing System</span>
        </a>

        {{-- Navbar Right --}}
        <div class="navbar-nav ms-auto d-flex flex-row align-items-center">

            {{-- User Profile Dropdown --}}
            <div class="nav-item dropdown">
                <button class="nav-link dropdown-toggle d-flex align-items-center border-0 bg-transparent p-0" id="userDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer; z-index: 1060; color: white;">
                    <i class="fas fa-user-circle me-2" style="font-size: 28px;"></i>
                    <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="z-index: 9999;">
                    {{-- My Profile --}}
                    <li>
                        <a class="dropdown-item" href="{{ route('staff.profile') }}">
                            <i class="fas fa-user-circle me-2"></i>My Profile
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
        const sidebar = document.querySelector('.sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const mainContent = document.querySelector('.main-content');
        const footer = document.querySelector('footer.footer');

        // ============================================
        // MOBILE Sidebar Toggle (< 992px)
        // ============================================
        const sidebarToggler = document.getElementById('sidebarToggler');

        if (sidebarToggler) {
            sidebarToggler.addEventListener('click', function() {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });
        }

        // ============================================
        // DESKTOP Sidebar Toggle (>= 992px)
        // ============================================
        const desktopToggler = document.getElementById('desktopSidebarToggler');
        const STORAGE_KEY = 'staff_sidebar_hidden';

        function applySidebarState(hidden) {
            if (hidden) {
                document.body.classList.add('sidebar-hidden');
            } else {
                document.body.classList.remove('sidebar-hidden');
            }
        }

        // Restore saved preference on page load
        const savedState = localStorage.getItem(STORAGE_KEY);
        if (savedState === 'true') {
            applySidebarState(true);
        }

        if (desktopToggler) {
            desktopToggler.addEventListener('click', function() {
                const isHidden = document.body.classList.toggle('sidebar-hidden');
                localStorage.setItem(STORAGE_KEY, isHidden);
            });
        }

        // ============================================
        // Help Function
        // ============================================
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
            if (href && href.length > 2 && href !== '#' && currentPath.includes(href)) {
                link.classList.add('active');
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
        z-index: 1050 !important;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        height: var(--navbar-height);
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    }

    /* Desktop sidebar toggle button */
    #desktopSidebarToggler {
        font-size: 1.15rem;
        padding: 0.35rem 0.6rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        opacity: 0.85;
    }
    #desktopSidebarToggler:hover {
        background-color: rgba(255, 255, 255, 0.2);
        opacity: 1;
    }

    /* Fix dropdown z-index */
    .navbar-nav .dropdown {
        position: relative;
        z-index: 1051 !important;
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

    .navbar-logo-wrapper {
        width: 40px;
        height: 40px;
        border-radius: 2px;
        overflow: hidden;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        margin-right: 0.75rem;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .navbar-logo-wrapper:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    }

    .navbar-logo {
        height: 100%;
        width: 100%;
        object-fit: cover;
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
        
        .navbar-logo-wrapper {
            width: 36px;
            height: 36px;
        }
    }

    /* Tablets (768px - 992px) */
    @media (max-width: 992px) {
        .navbar-custom .navbar-brand {
            font-size: 0.9rem;
        }
        
        .navbar-logo-wrapper {
            width: 34px;
            height: 34px;
        }
    }

    /* Large phones and small tablets (576px - 768px) */
    @media (max-width: 768px) {
        .navbar-custom .navbar-brand {
            font-size: 0.8rem;
        }
        
        .navbar-logo-wrapper {
            width: 32px;
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
            font-size: 0.7rem;
        }
        
        .navbar-logo-wrapper {
            width: 28px;
            height: 28px;
            margin-right: 0.4rem;
            border-radius: 7px;
        }
    }

    /* Extra small phones (up to 400px) */
    @media (max-width: 400px) {
        .navbar-custom .navbar-brand {
            font-size: 0.65rem;
        }
        
        .navbar-logo-wrapper {
            width: 25px;
            height: 25px;
            margin-right: 0.3rem;
            border-radius: 6px;
        }
        
        .navbar-title {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    }

    /* iPhone SE and similar (320px - 375px) */
    @media (max-width: 375px) {
        .navbar-custom .navbar-brand {
            font-size: 0.6rem;
        }
        
        .navbar-logo-wrapper {
            width: 22px;
            height: 22px;
            margin-right: 0.25rem;
            border-radius: 5px;
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
            font-size: 0.75rem;
        }
        
        .navbar-logo-wrapper {
            width: 28px;
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