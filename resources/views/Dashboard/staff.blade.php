@extends('layouts.staff')

@section('title', 'Dashboard Staff')

@section('content')
    <div class="welcome-banner">
        <h2>Selamat Datang, <strong>{{ Auth::user()->name }}</strong></h2>
        <p>Departemen <span class="badge bg-light text-primary">{{ Auth::user()->department }}</span></p>
    </div>

    {{-- Statistik Cards --}}
    <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="dashboard-card">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(79, 70, 229, 0.1); color: var(--primary-color);">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <div class="stat-number text-primary">12</div>
                        <div class="stat-label">Total Tiket</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="dashboard-card">
                    <div class="stat-card">
                        <div class="stat-icon"
                            style="background: rgba(245, 158, 11, 0.1); color: var(--warning-color);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-number text-warning">3</div>
                        <div class="stat-label">Menunggu</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="dashboard-card">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: var(--info-color);">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div class="stat-number text-info">2</div>
                        <div class="stat-label">Sedang Diproses</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="dashboard-card">
                    <div class="stat-card">
                        <div class="stat-icon"
                            style="background: rgba(16, 185, 129, 0.1); color: var(--success-color);">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-number text-success">7</div>
                        <div class="stat-label">Selesai</div>
                    </div>
                </div>
            </div>
        </div>

    {{-- Quick Actions --}}
    <div class="row mb-4">
            <div class="col-12">
                <div class="dashboard-card">
                    <div class="card-body p-4">
                        <h5 class="mb-4"><i class="fas fa-bolt me-2"></i>Aksi Cepat</h5>
                        <div class="row">
                            <!-- Buat Tiket Baru -->
                            <div class="col-lg-3 col-md-6 mb-3">
    <a href="{{ route('tickets.create') }}" class="quick-action-btn">
        <div class="quick-action-icon"
            style="background: rgba(79, 70, 229, 0.1); color: var(--primary-color);">
            <i class="fas fa-plus"></i>
        </div>
        <h6 class="mb-1">Buat Tiket Baru</h6>
        <small class="text-muted">Laporkan masalah atau permintaan</small>
    </a>
</div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
