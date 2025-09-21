@extends('layouts.staff')

@section('title', 'Daftar Tiket')
@vite(['resources/css/list-tiket.css','resources/js/list-tiket.js'])

@section('content')
<div class="container">
    <!-- Filters Section -->
    <div class="filters-section fade-in">
        <div class="filters-row">
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Cari tiket..." id="searchInput">
                <i class="fas fa-search search-icon"></i>
            </div>
            <select class="filter-select" id="statusFilter">
                <option value="">Semua Status</option>
                <option value="open">Terbuka</option>
                <option value="progress">Dalam Proses</option>
                <option value="resolved">Selesai</option>
                <option value="closed">Ditutup</option>
            </select>
            <select class="filter-select" id="priorityFilter">
                <option value="">Semua Prioritas</option>
                <option value="high">Tinggi</option>
                <option value="medium">Sedang</option>
                <option value="low">Rendah</option>
            </select>

            <!-- ✅ tombol ini sekarang langsung buka modal -->
            <button type="button" class="create-btn" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                <i class="fas fa-plus"></i>
                Buat Tiket Baru
            </button>
        </div>
    </div>

    <!-- Tickets List -->
    <div class="tickets-container fade-in">
        <div class="tickets-header">
            <i class="fas fa-list"></i>
            Daftar Tiket (12 tiket ditemukan)
        </div>
        
        <div id="ticketsContent">
            <div class="loading" id="loadingState" style="display: none;">
                <div class="spinner"></div>
                <p>Memuat tiket...</p>
            </div>
            <div id="ticketsList"></div>
            <div class="empty-state" id="emptyState" style="display: none;">
                <div class="empty-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <h3 class="empty-title">Tidak ada tiket ditemukan</h3>
                <p class="empty-text">Belum ada tiket yang sesuai</p>

                <!-- ✅ tombol empty state juga buka modal -->
                <button type="button" class="quick-action-btn" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                    <i class="fas fa-plus"></i>
                    Buat Tiket Pertama
                </button>
            </div>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <div class="pagination-info">
                Menampilkan 1-10 dari 12 tiket
            </div>
            <div class="pagination-controls">
                <button class="page-btn" disabled>
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="page-btn active">1</button>
                <button class="page-btn">2</button>
                <button class="page-btn">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ✅ Include modal form-ticket --}}
@include('staff.modals.form-ticket')

@endsection
