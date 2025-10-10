@extends('layouts.it')

@section('title','Daftar Tiket IT')
<meta name="csrf-token" content="{{ csrf_token() }}">
@vite(['resources/css/list-tiket.css', 'resources/js/it.js'])

@section('content')
<div class="container py-4">

    <h2 class="fw-bold mb-3"><i class="fas fa-ticket-alt text-primary me-2"></i> Daftar Tiket IT</h2>

    {{-- ================= FILTER ================= --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('it.index-ticket') }}" class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari tiket..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach(['waiting','in_progress','pending'] as $status)
                            <option value="{{ $status }}" @selected(request('status')==$status)>
                                {{ ucfirst(str_replace('_',' ',$status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(request('category_id')==$cat->id)>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary"><i class="fas fa-filter me-1"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= TABEL TIKET ================= --}}
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>ID Tiket</th>
                        <th class="text-start">Deskripsi</th>
                        <th>Departemen</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Prioritas</th>
                        <th>Dibuat Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr id="ticket-row-{{ $ticket->id }}">
                            <td>#{{ $ticket->ticket_id }}</td>
                            <td class="text-start">{{ Str::limit($ticket->description, 60) }}</td>
                            <td>{{ $ticket->user->department ?? '-' }}</td>
                            <td>{{ $ticket->category->name ?? '-' }}</td>
                            <td>
                                <select class="form-select form-select-sm update-ticket-field"
                                        data-id="{{ $ticket->id }}"
                                        data-field="status"
                                        data-original-value="{{ $ticket->status }}">
                                    @foreach(['waiting','in_progress','pending','resolved','closed'] as $status)
                                        <option value="{{ $status }}" @selected($ticket->status==$status)>
                                            {{ ucfirst(str_replace('_',' ',$status)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-select form-select-sm update-ticket-field"
                                        data-id="{{ $ticket->id }}"
                                        data-field="priority"
                                        data-original-value="{{ $ticket->priority }}">
                                    @foreach(['low','medium','high','urgent'] as $priority)
                                        <option value="{{ $priority }}" @selected($ticket->priority==$priority)>
                                            {{ ucfirst($priority) }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>{{ $ticket->user->name ?? '-' }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary btn-detail-ticket"
                                        data-id="{{ $ticket->id }}">
                                    <i class="fas fa-eye me-1"></i> Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-muted">Tidak ada tiket ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <span>Menampilkan {{ $tickets->firstItem() ?? 0 }} - {{ $tickets->lastItem() ?? 0 }} dari {{ $tickets->total() }}</span>
            {{ $tickets->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- ================= MODAL DETAIL ================= --}}
<div class="modal fade" id="detailTicketModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-bold text-primary">
          <i class="fas fa-ticket-alt me-2"></i> Detail Tiket
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="d_loader" class="text-center my-4">
          <div class="spinner-border text-primary"></div>
        </div>
        <div id="d_content" class="d-none">
          <table class="table table-borderless align-middle mb-0">
            <tr><th width="25%">ID Tiket</th><td><span id="d_ticket_id"></span></td></tr>
            <tr><th>Dibuat oleh</th><td><span id="d_user"></span></td></tr>
            <tr><th>Departemen</th><td><span id="d_department"></span></td></tr>
            <tr><th>Kategori</th><td><span id="d_category"></span></td></tr>
            <tr><th>Status</th><td><span class="badge bg-warning text-dark" id="d_status"></span></td></tr>
            <tr><th>Prioritas</th><td><span class="badge bg-info text-dark" id="d_priority"></span></td></tr>
            <tr><th>Deskripsi</th><td><span id="d_description"></span></td></tr>
            <tr><th>Tanggal</th><td><span id="d_created"></span></td></tr>
            <tr><th>Lampiran</th><td><div id="d_attachments" class="d-flex flex-wrap gap-2"></div></td></tr>
          </table>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function(){

    // ==========================================================
    // 🔔 TOAST HELPER
    // ==========================================================
    const toastEl = document.getElementById('liveToast');
    const bsToast = new bootstrap.Toast(toastEl);
    const showToast = (msg, success = true) => {
        document.getElementById('toast-body-message').textContent = msg;
        toastEl.querySelector('i').className = success
            ? 'fas fa-check-circle text-success me-2'
            : 'fas fa-times-circle text-danger me-2';
        bsToast.show();
    };

    // ==========================================================
    // ⚙️ UPDATE STATUS / PRIORITY FIELD
    // ==========================================================
    document.body.addEventListener('change', async (e) => {
        if (!e.target.classList.contains('update-ticket-field')) return;
        const select = e.target;
        const id = select.dataset.id;
        const field = select.dataset.field;
        const value = select.value;
        const old = select.dataset.originalValue;

        if (!confirm(`Ubah ${field} menjadi "${value}"?`)) {
            select.value = old;
            return;
        }

        try {
            const res = await fetch(`/it/tickets/${id}/update-field`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ field, value })
            });
            const data = await res.json();
            if (data.success) {
                showToast(data.message);
                select.dataset.originalValue = value;
            } else {
                showToast(data.message, false);
                select.value = old;
            }
        } catch (err) {
            console.error(err);
            showToast('Server error', false);
            select.value = old;
        }
    });

    // ==========================================================
    // 🎫 DETAIL MODAL HANDLER
    // ==========================================================
    document.body.addEventListener('click', async (e) => {
        const btn = e.target.closest('.btn-detail-ticket');
        if (!btn) return;

        // pastikan pakai ID modal yang benar
        const modalEl = document.getElementById('detailTicketModal');
        const modal = new bootstrap.Modal(modalEl);
        const modalBody = modalEl.querySelector('.modal-body');

        modal.show();
        modalBody.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary"></div>
                <p class="mt-2">Memuat detail tiket...</p>
            </div>
        `;

        try {
            const res = await fetch(`/it/tickets/${btn.dataset.id}`);
            const ticket = await res.json();
            const user = ticket.user || {};
            const cat = ticket.category || {};
            let attachments = [];

            // Jika "attachments" dikirim dalam bentuk string JSON
            try {
                attachments = Array.isArray(ticket.attachments)
                    ? ticket.attachments
                    : JSON.parse(ticket.attachments || '[]');
            } catch {
                attachments = [];
            }

            let filesHTML = `<span class="text-muted">Tidak ada lampiran</span>`;
            if (attachments.length > 0) {
                filesHTML = attachments.map(file => {
                    const url = `/storage/${file}`;
                    const ext = file.split('.').pop().toLowerCase();
                    if (['jpg','jpeg','png','gif','webp'].includes(ext)) {
                        return `
                            <img src="${url}" class="img-thumbnail me-2 mb-2"
                                 style="max-width:150px;cursor:pointer"
                                 onclick="window.open('${url}','_blank')">
                        `;
                    }
                    return `<a href="${url}" target="_blank">${file.split('/').pop()}</a>`;
                }).join('');
            }

            modalBody.innerHTML = `
                <div id="detail-content">
                    <table class="table table-borderless align-middle mb-0">
                        <tr><th width="25%">ID Tiket</th><td>#${ticket.ticket_id}</td></tr>
                        <tr><th>Dibuat oleh</th><td>${user.name || '-'}</td></tr>
                        <tr><th>Departemen</th><td>${user.department || '-'}</td></tr>
                        <tr><th>Kategori</th><td>${cat.name || '-'}</td></tr>
                        <tr><th>Status</th><td><span class="badge bg-secondary">${ticket.status}</span></td></tr>
                        <tr><th>Prioritas</th><td><span class="badge bg-info text-dark">${ticket.priority}</span></td></tr>
                        <tr><th>Deskripsi</th><td style="white-space: pre-wrap;">${ticket.description}</td></tr>
                        <tr><th>Dibuat pada</th><td>${ticket.created_at || '-'}</td></tr>
                        <tr><th>Lampiran</th><td>${filesHTML}</td></tr>
                    </table>
                </div>
            `;
        } catch (err) {
            console.error(err);
            modalBody.innerHTML = `<div class="alert alert-danger">Gagal memuat detail tiket.</div>`;
        }
    });
});
</script>
@endpush
