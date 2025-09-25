@extends('layouts.staff')

@section('title', 'Dashboard Staff')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<div class="welcome-banner">
    <h2>Selamat Datang, <strong>{{ Auth::user()->name }}</strong></h2>
    <p>Departemen <span class="badge bg-light text-primary">{{ Auth::user()->department }}</span></p>
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
                        <button type="button" class="quick-action-btn" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                            <div class="quick-action-icon"
                                style="background: rgba(79, 70, 229, 0.1); color: var(--primary-color);">
                                <i class="fas fa-plus"></i>
                            </div>
                            <h6 class="mb-1">Buat Tiket Baru</h6>
                            <small class="text-muted">Laporkan masalah atau permintaan</small>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal form-ticket --}}
@include('staff.modals.form-ticket')

@endsection
