<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <!-- Mobile Sidebar Toggle -->
        <button class="btn navbar-toggler d-lg-none me-3" type="button" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Brand -->
        <a class="navbar-brand" href="#">
            <img src="{{ asset('assets/image/logo-ktu-removebg.png') }}" alt="KTU Logo" class="me-2"
                style="height: 40px; width: auto; object-fit: contain;">
            IT Ticketing System
        </a>

        <!-- Navbar Right -->
        <div class="navbar-nav ms-auto d-flex flex-row">
            {{-- Notifikasi --}}
            <div class="nav-item dropdown">
                    <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                        <li class="px-3 py-2 border-bottom">
                            <h6 class="mb-0">Notifikasi</h6>
                        </li>
                        <li><a class="dropdown-item" href="#">
                                <div class="d-flex">
                                    <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                    <div>
                                        <strong>Tiket #TKT-003</strong><br>
                                        <small class="text-muted">Password email berhasil direset</small>
                                    </div>
                                </div>
                            </a></li>
                        <li><a class="dropdown-item" href="#">
                                <div class="d-flex">
                                    <i class="fas fa-cogs text-info me-2 mt-1"></i>
                                    <div>
                                        <strong>Tiket #TKT-002</strong><br>
                                        <small class="text-muted">Sedang diproses oleh tim IT</small>
                                    </div>
                                </div>
                            </a></li>
                        <li><a class="dropdown-item" href="#">
                                <div class="d-flex">
                                    <i class="fas fa-clock text-warning me-2 mt-1"></i>
                                    <div>
                                        <strong>Tiket #TKT-001</strong><br>
                                        <small class="text-muted">Menunggu konfirmasi</small>
                                    </div>
                                </div>
                            </a></li>
                        <li class="px-3 py-2 border-top text-center">
                            <a href="#" class="text-decoration-none">Lihat semua notifikasi</a>
                        </li>
                    </ul>
                </div>

            {{-- User Profile --}}
            <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i>
                        <span class="d-none d-sm-inline">{{Auth::user()->name}}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="px-3 py-2 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center"
                                    style="width: 35px; height: 35px;">
                                    <span class="text-white fw-bold">JD</span>
                                </div>
                                <div>
                                    <strong>{{ Auth::user()->name }}</strong><br>
                                    <small class="text-muted">{{ Auth::user()->email }}</small>
                                </div>
                            </div>
                        </li>
                        <li><a class="dropdown-item" href="#" onclick="showProfile()">
                                <i class="fas fa-user me-2"></i>Profil Saya
                            </a></li>
                        <li><a class="dropdown-item" href="#" onclick="showSettings()">
                                <i class="fas fa-cog me-2"></i>Pengaturan
                            </a></li>
                        <li><a class="dropdown-item" href="#" onclick="showHelp()">
                                <i class="fas fa-question-circle me-2"></i>Bantuan
                            </a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); if (confirm('Apakah Anda yakin ingin logout?')) { document.getElementById('logout-form').submit(); }">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>


                        </li>
                    </ul>
                </div>
        </div>
    </div>
</nav>
