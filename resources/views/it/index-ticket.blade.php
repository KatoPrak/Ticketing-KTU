@extends('layouts.it')

@section('title', 'Daftar Tiket IT')
@vite(['resources/css/list-tiket.css','resources/js/list-tiket.js'])

@section('content')
<div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title text-primary fw-bold">
            <i class="fas fa-list-alt me-2"></i> Daftar Tiket IT
        </h2>
    </div>

    <!-- Filters (langsung di sini, tidak pakai partial) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body bg-light rounded-3">
            <form method="GET" action="{{ route('it.index-ticket') }}" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control border-primary" placeholder="🔍 Cari tiket..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select border-primary">
                        <option value="">Semua Status</option>
                        <option value="open" {{ request('status')=='open'?'selected':'' }}>Terbuka</option>
                        <option value="progress" {{ request('status')=='progress'?'selected':'' }}>Dalam Proses</option>
                        <option value="done" {{ request('status')=='done'?'selected':'' }}>Selesai</option>
                        <option value="closed" {{ request('status')=='closed'?'selected':'' }}>Ditutup</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-select border-primary">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id')==$cat->id?'selected':'' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Tickets -->
    <div class="card border-0 shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-primary text-center">
                    <tr>
                        <th>ID</th>
                        <th>Deskripsi</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Prioritas</th>
                        <th>Dibuat Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr>
                        <td class="fw-bold text-primary">#{{ $ticket->id }}</td>
                        <td>{{ $ticket->description }}</td>
                        <td><span class="badge bg-info text-dark">{{ $ticket->category->name ?? '-' }}</span></td>
                        <td>
                            <form action="{{ route('it.tickets.update', $ticket->id) }}" method="POST">
                                @csrf @method('PUT')
                                <select name="status"
                                    class="form-select form-select-sm border-0 bg-light fw-semibold text-primary"
                                    onchange="this.form.submit()">
                                    <option value="open" {{ $ticket->status=='open'?'selected':'' }}>Terbuka</option>
                                    <option value="progress" {{ $ticket->status=='progress'?'selected':'' }}>Dalam
                                        Proses</option>
                                    <option value="done" {{ $ticket->status=='done'?'selected':'' }}>Selesai</option>
                                    <option value="closed" {{ $ticket->status=='closed'?'selected':'' }}>Ditutup
                                    </option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <form action="{{ route('it.tickets.update', $ticket->id) }}" method="POST">
                                @csrf @method('PUT')
                                <select name="priority"
                                    class="form-select form-select-sm border-0 bg-light fw-semibold text-danger"
                                    onchange="this.form.submit()">
                                    <option value="low" {{ $ticket->priority=='low'?'selected':'' }}>Rendah</option>
                                    <option value="medium" {{ $ticket->priority=='medium'?'selected':'' }}>Sedang
                                    </option>
                                    <option value="high" {{ $ticket->priority=='high'?'selected':'' }}>Tinggi</option>
                                </select>
                            </form>
                        </td>
                        <td>{{ $ticket->user->name ?? 'Unknown' }}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-detail-ticket"
                                data-id="{{ $ticket->id }}" data-bs-toggle="modal" data-bs-target="#ticketDetailModal">
                                <i class="fas fa-eye me-1"></i> Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">😕 Tidak ada tiket ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-light d-flex justify-content-between align-items-center">
            <span class="text-muted">
                Showing {{ $tickets->firstItem() ?? 0 }} to {{ $tickets->lastItem() ?? 0 }} of {{ $tickets->total() }}
            </span>
            <div>{{ $tickets->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
</div>

<!-- Modal tetap ada di sini, tapi isinya akan di-load dari ticket-detail.blade.php -->
<div class="modal fade" id="ticketDetailModal" tabindex="-1" aria-labelledby="ticketDetailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ticketDetailModalLabel"><i class="fas fa-ticket-alt me-2 text-primary"></i>
                    Detail Tiket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="ticketDetailContent">
                <p class="text-center text-muted">Memuat detail tiket...</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const buttons = document.querySelectorAll(".btn-detail-ticket");
    const modalContent = document.getElementById("ticketDetailContent");

    buttons.forEach(btn => {
        btn.addEventListener("click", function () {
            const ticketId = this.dataset.id;
            modalContent.innerHTML = `<p class="text-center text-muted">Memuat detail tiket...</p>`;

            fetch(`/it/tickets/${ticketId}`)
                .then(res => res.text())
                .then(html => modalContent.innerHTML = html)
                .catch(() => {
                    modalContent.innerHTML = `<p class="text-danger text-center">Gagal memuat detail tiket.</p>`;
                });
        });
    });
});

</script>
@endpush
