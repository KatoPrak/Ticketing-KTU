<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h5><i class="fas fa-tachometer-alt me-2"></i>Menu Utama</h5>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('staff.dashboard') }}" class="{{ Request::is('dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('staff.tickets.index') }}" class="{{ Request::is('list-tiket') ? 'active' : '' }}">
                <i class="fas fa-ticket-alt"></i>
                Tiket Saya
            </a>
        </li>
        <li>
            <a href="#" class="{{ Request::is('knowledge-base') ? 'active' : '' }}">
                <i class="fas fa-book"></i>
                Knowledge Base
            </a>
        </li>
        <li>
            <a href="#" class="{{ Request::is('faq') ? 'active' : '' }}">
                <i class="fas fa-question-circle"></i>
                FAQ
            </a>
        </li>
        <li>
            <a href="#" class="{{ Request::is('profile') ? 'active' : '' }}">
                <i class="fas fa-user"></i>
                Profil Saya
            </a>
        </li>
        <li>
            <a href="#" class="{{ Request::is('settings') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>
                Pengaturan
            </a>
        </li>
        <li>
            <a href="#" class="{{ Request::is('help') ? 'active' : '' }}">
                <i class="fas fa-life-ring"></i>
                Bantuan & Dukungan
            </a>
        </li>
    </ul>
</div>
