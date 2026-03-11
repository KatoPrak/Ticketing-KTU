<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h5><i class="fas fa-tachometer-alt me-2"></i><span class="menu-text">Main Menu</span></h5>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('staff.dashboard') }}" 
               class="{{ Request::is('staff/dashboard') ? 'active' : '' }}"
               title="Dashboard">
                <i class="fas fa-home"></i>
                <span class="menu-text">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="{{ route('staff.tickets.index') }}" 
               class="{{ Request::is('staff/tickets') ? 'active' : '' }}"
               title="My Tickets">
                <i class="fas fa-ticket-alt"></i>
                <span class="menu-text">My Tickets</span>
            </a>
        </li>

        <li class="nav-item" style="position: relative;">
            <a href="#settingsMenu" data-bs-toggle="collapse" id="settingsToggle" class="nav-link d-flex align-items-center {{ Request::is('staff/profile') ? 'active' : 'collapsed' }}" aria-expanded="{{ Request::is('staff/profile') ? 'true' : 'false' }}" title="Settings">
                <i class="fas fa-cog me-2"></i>
                <span class="menu-text">Settings</span>
                <i class="fas fa-chevron-down ms-auto sub-menu-arrow"></i>
            </a>

            {{-- Normal sub-menu (visible when sidebar expanded) --}}
            <ul id="settingsMenu" class="collapse list-unstyled ps-3 show-if-active {{ Request::is('staff/profile') ? 'show' : '' }}">
                <li class="mt-1">
                    <a href="{{ route('staff.profile') }}" 
                       class="d-flex align-items-center p-2 {{ Request::is('staff/profile') ? 'active' : '' }}" style="font-size: 0.9rem;"
                       title="My Profile">
                        <i class="fas fa-user-circle me-2"></i>
                        <span class="menu-text">My Profile</span>
                    </a>
                </li>
                <li class="mt-1">
                    <a href="{{ route('logout') }}" 
                       class="d-flex align-items-center p-2 text-danger" style="font-size: 0.9rem;"
                       onclick="event.preventDefault(); if (confirm('Are you sure you want to log out?')) document.getElementById('logout-form').submit();"
                       title="Logout">
                        <i class="fas fa-sign-out-alt me-2"></i>
                        <span class="menu-text">Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>

            {{-- Floating popup (visible when sidebar collapsed + settings clicked) --}}
            <div class="collapsed-settings-popup" id="collapsedSettingsPopup">
                <a href="{{ route('staff.profile') }}" class="popup-icon-btn {{ Request::is('staff/profile') ? 'active' : '' }}" title="My Profile">
                    <i class="fas fa-user-circle"></i>
                </a>
                <a href="{{ route('logout') }}" class="popup-icon-btn logout-btn" title="Logout"
                   onclick="event.preventDefault(); if (confirm('Are you sure you want to log out?')) document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
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

    /* ===== COLLAPSED SETTINGS POPUP (icon-only) ===== */
    .collapsed-settings-popup {
        display: none;
        position: absolute;
        left: 100%;
        top: 50%;
        transform: translateY(-50%);
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        z-index: 2000;
        padding: 0.4rem;
        margin-left: 10px;
        gap: 0.35rem;
        flex-direction: row;
        align-items: center;
    }

    .collapsed-settings-popup.show {
        display: flex;
    }

    .collapsed-settings-popup .popup-icon-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        color: #4b5563;
        text-decoration: none;
        font-size: 1.15rem;
        transition: all 0.2s ease;
        background: #f3f4f6;
    }

    .collapsed-settings-popup .popup-icon-btn:hover {
        background: rgba(102, 126, 234, 0.15);
        color: #667eea;
        transform: scale(1.1);
    }

    .collapsed-settings-popup .popup-icon-btn.active {
        background: #667eea;
        color: #fff;
    }

    .collapsed-settings-popup .popup-icon-btn.logout-btn {
        color: #ef4444;
        background: #fef2f2;
    }

    .collapsed-settings-popup .popup-icon-btn.logout-btn:hover {
        background: #fee2e2;
        color: #dc2626;
        transform: scale(1.1);
    }

    /* Arrow pointing left toward sidebar */
    .collapsed-settings-popup::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 50%;
        transform: translateY(-50%) rotate(45deg);
        width: 12px;
        height: 12px;
        background: #fff;
        border-left: 1px solid #e5e7eb;
        border-bottom: 1px solid #e5e7eb;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const settingsToggle = document.getElementById('settingsToggle');
    const collapsedPopup = document.getElementById('collapsedSettingsPopup');

    if (!settingsToggle || !collapsedPopup) return;

    // Store original Bootstrap toggle attribute
    const originalToggle = settingsToggle.getAttribute('data-bs-toggle');
    const originalHref = settingsToggle.getAttribute('href');

    function isCollapsedMode() {
        return document.body.classList.contains('sidebar-hidden') && window.innerWidth >= 993;
    }

    // Disable/enable Bootstrap collapse based on sidebar state
    function updateSettingsToggleBehavior() {
        if (isCollapsedMode()) {
            // Remove Bootstrap collapse behavior
            settingsToggle.removeAttribute('data-bs-toggle');
            settingsToggle.setAttribute('href', 'javascript:void(0)');
        } else {
            // Restore Bootstrap collapse behavior
            settingsToggle.setAttribute('data-bs-toggle', originalToggle);
            settingsToggle.setAttribute('href', originalHref);
            collapsedPopup.classList.remove('show');
        }
    }

    // Run on page load
    updateSettingsToggleBehavior();

    // Handle settings click in collapsed mode
    settingsToggle.addEventListener('click', function(e) {
        if (isCollapsedMode()) {
            e.preventDefault();
            e.stopPropagation();

            // Position the popup next to the icon using fixed positioning
            const rect = settingsToggle.getBoundingClientRect();
            collapsedPopup.style.position = 'fixed';
            collapsedPopup.style.left = (rect.right + 10) + 'px';
            collapsedPopup.style.top = (rect.top + rect.height / 2) + 'px';
            collapsedPopup.style.transform = 'translateY(-50%)';

            collapsedPopup.classList.toggle('show');
        }
    });

    // Close popup when clicking anywhere outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#settingsToggle') && !e.target.closest('.collapsed-settings-popup')) {
            collapsedPopup.classList.remove('show');
        }
    });

    // Update behavior when sidebar is toggled
    const desktopToggler = document.getElementById('desktopSidebarToggler');
    if (desktopToggler) {
        desktopToggler.addEventListener('click', function() {
            // Small delay to let the class toggle happen first
            setTimeout(updateSettingsToggleBehavior, 50);
        });
    }
});
</script>
