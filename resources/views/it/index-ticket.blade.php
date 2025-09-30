@extends('layouts.it')

@section('title', 'Daftar Tiket IT')
@vite(['resources/css/list-tiket.css','resources/js/list-tiket.js'])

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">
            <i class="fas fa-ticket-alt text-primary me-2"></i> Daftar Tiket IT
        </h2>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            {{-- ✅ PERBAIKAN: Nama route di action form diperbarui --}}
            <form method="GET" action="{{ route('it.index-ticket') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari tiket..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="waiting" @selected(request('status') == 'waiting')>Menunggu</option>
                        <option value="in_progress" @selected(request('status') == 'in_progress')>Sedang Dikerjakan</option>
                        <option value="pending" @selected(request('status') == 'pending')>Tertunda</option>
                        <option value="resolve" @selected(request('status') == 'resolve')>Selesai</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary"><i class="fas fa-filter me-1"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body table-responsive">
            <table class="table align-middle">
                <thead class="table-light text-center">
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
                        <td class="fw-semibold text-primary">#{{ $ticket->id }}</td>
                        <td>{{ Str::limit($ticket->description, 50) }}</td>
                        <td><span class="badge rounded-pill bg-info-subtle text-info px-3">{{ $ticket->category->name ?? '-' }}</span></td>
                        <td style="min-width: 180px;">
                            <select name="status" class="form-select form-select-sm update-ticket-field" data-id="{{ $ticket->id }}" data-field="status">
                                <option value="waiting" @selected($ticket->status == 'waiting')>Menunggu</option>
                                <option value="in_progress" @selected($ticket->status == 'in_progress')>Sedang Dikerjakan</option>
                                <option value="pending" @selected($ticket->status == 'pending')>Tertunda</option>
                                <option value="resolve" @selected($ticket->status == 'resolve')>Selesai</option>
                            </select>
                        </td>
                        <td style="min-width: 150px;">
                            <select name="priority" class="form-select form-select-sm update-ticket-field text-danger fw-semibold" data-id="{{ $ticket->id }}" data-field="priority">
                                <option value="low" @selected($ticket->priority == 'low')>Rendah</option>
                                <option value="medium" @selected($ticket->priority == 'medium')>Sedang</option>
                                <option value="high" @selected($ticket->priority == 'high')>Tinggi</option>
                                <option value="urgent" @selected($ticket->priority == 'urgent')>Mendesak</option>
                            </select>
                        </td>
                        <td>{{ $ticket->user->name ?? 'Unknown' }}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill btn-detail-ticket" 
                                data-bs-toggle="modal" data-bs-target="#ticketDetailModal" data-id="{{ $ticket->id }}">
                                <i class="fas fa-eye me-1"></i> Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4"><i class="fas fa-info-circle me-1"></i> Tidak ada tiket ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <span class="text-muted small">Showing {{ $tickets->firstItem() ?? 0 }} - {{ $tickets->lastItem() ?? 0 }} of {{ $tickets->total() }}</span>
            <div>{{ $tickets->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
</div>

<div class="modal fade" id="ticketDetailModal" tabindex="-1" aria-labelledby="ticketDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div id="ticketDetailContent">
            <div class="modal-content"><div class="modal-body text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div></div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="fas fa-check-circle text-success me-2"></i>
            <strong class="me-auto">Notifikasi</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toast-body-message"></div>
    </div>
</div>
@endsection

@push('scripts')
@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. Ambil semua data tiket dari PHP ke JavaScript
    // Variabel $tickets dari controller di-encode menjadi JSON yang bisa dibaca JS
    const ticketsData = @json($tickets->items());

    const liveToastEl = document.getElementById('liveToast');
    const liveToast = new bootstrap.Toast(liveToastEl);
    
    // Helper untuk notifikasi toast (tidak berubah)
    function showToast(message, success = true) {
        const toastBody = document.getElementById('toast-body-message');
        toastBody.textContent = message;
        const icon = liveToastEl.querySelector('.toast-header i');
        icon.className = success ? 'fas fa-check-circle text-success me-2' : 'fas fa-times-circle text-danger me-2';
        liveToast.show();
    }

    // --- UPDATE STATUS & PRIORITAS VIA AJAX (Tidak berubah) ---
    document.body.addEventListener('change', function(e) {
        if (e.target.classList.contains('update-ticket-field')) {
            const select = e.target;
            const ticketId = select.dataset.id;
            const field = select.dataset.field;
            const value = select.value;
            const originalValue = [...select.options].find(option => option.defaultSelected).value;
            select.disabled = true;

            fetch(`/it/tickets/${ticketId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ [field]: value })
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if(ok) {
                    showToast(data.message);
                    select.querySelector('option[selected]').removeAttribute('selected');
                    select.querySelector(`option[value="${value}"]`).setAttribute('selected', true);
                } else {
                    showToast(data.message || 'Gagal memperbarui tiket.', false);
                    select.value = originalValue;
                }
            })
            .catch(() => {
                showToast('Terjadi kesalahan koneksi.', false)
                select.value = originalValue;
            })
            .finally(() => select.disabled = false);
        }
    });

    // --- 2. MODAL DETAIL TIKET (VERSI BARU - TANPA FETCH) ---
    const detailModal = document.getElementById('ticketDetailModal');
    if(detailModal) {
        detailModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const ticketId = button.dataset.id;
            const modalContent = document.getElementById("ticketDetailContent");
            
            // Cari data tiket yang sesuai di dalam variabel ticketsData
            const ticket = ticketsData.find(t => t.id == ticketId);

            if (!ticket) {
                modalContent.innerHTML = `<div class="modal-content"><div class="modal-body text-danger">Data tiket tidak ditemukan.</div></div>`;
                return;
            }

            // Bangun (render) HTML untuk konten modal secara dinamis
            const attachments = ticket.attachments || [];
            let attachmentsHtml = '';
            if (attachments.length > 0) {
                attachmentsHtml += `<div class="info-box"><h6 class="text-muted mb-2"><i class="fas fa-paperclip me-2"></i>Lampiran</h6><div class="d-flex flex-wrap">`;
                attachments.forEach(file => {
                    attachmentsHtml += `
                        <a href="/storage/${file}" target="_blank" class="me-2 mb-2">
                            <img src="/storage/${file}" alt="lampiran" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                        </a>`;
                });
                attachmentsHtml += `</div></div>`;
            }

            const statusColors = { waiting: 'warning text-dark', in_progress: 'info text-dark', resolve: 'success', pending: 'secondary' };
            const priorityColors = { low: 'success', medium: 'primary', high: 'danger', urgent: 'danger' };
            const statusText = (ticket.status || '').replace('_', ' ');

            const modalHtml = `
                <div class="modal-content border-0 shadow-lg rounded-3">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-ticket-alt me-2"></i> Detail Tiket #${ticket.id}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <div class="col-md-7">
                                <div class="info-box mb-3"><h6 class="text-muted mb-1"><i class="fas fa-align-left me-2"></i>Deskripsi</h6><p class="fw-semibold" style="white-space: pre-wrap;">${ticket.description}</p></div>
                                ${attachmentsHtml}
                            </div>
                            <div class="col-md-5">
                                <div class="info-box mb-3"><h6 class="text-muted mb-1"><i class="fas fa-flag me-2"></i>Status</h6><span class="badge rounded-pill fs-6 bg-${statusColors[ticket.status] || 'secondary'}">${statusText}</span></div>
                                <div class="info-box mb-3"><h6 class="text-muted mb-1"><i class="fas fa-exclamation-circle me-2"></i>Prioritas</h6><span class="badge rounded-pill fs-6 bg-${priorityColors[ticket.priority] || 'secondary'}">${ticket.priority}</span></div>
                                <div class="info-box mb-3"><h6 class="text-muted mb-1"><i class="fas fa-layer-group me-2"></i>Kategori</h6><p class="fw-semibold">${ticket.category ? ticket.category.name : '-'}</p></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <small class="text-muted me-auto">
                            Dibuat oleh: <span class="fw-semibold">${ticket.user ? ticket.user.name : 'Unknown'}</span><br>
                            <i class="far fa-calendar-alt me-1"></i> ${new Date(ticket.created_at).toLocaleString('id-ID')}
                        </small>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Tutup</button>
                    </div>
                </div>
            `;

            // Masukkan HTML yang sudah jadi ke dalam kerangka modal
            modalContent.innerHTML = modalHtml;
        });
    }
});
</script>
@endpush