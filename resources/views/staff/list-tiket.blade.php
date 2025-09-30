@extends('layouts.staff')

@section('title', 'Daftar Tiket')
@vite(['resources/css/list-tiket.css','resources/js/list-tiket.js'])

@section('content')
<div class="container my-2"><!-- tambahin margin atas-bawah -->
    
    {{-- ================== FILTER SECTION ================== --}}
    <div class="filters-section fade-in small-container mb-4"><!-- tambahin jarak bawah -->
        <div class="filters-row d-flex align-items-center justify-content-between flex-wrap gap-2">
            <form method="GET" action="{{ route('staff.tickets.index') }}" id="filterForm" class="flex-grow-1" style="max-width:1000px;">
                <div class="search-box">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="search-input form-control" 
                           placeholder="Cari tiket..." id="searchInput">
                    <i class="fas fa-search search-icon"></i>
                </div>
            </form>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                <i class="fas fa-plus"></i> Buat Tiket Baru
            </button>
        </div>
    </div>

    {{-- ================== DAFTAR TIKET (AKTIF) ================== --}}
    <div class="tickets-container fade-in mb-5"><!-- tambahin margin bawah -->
        <div class="tickets-header mb-3">
            <i class="fas fa-list"></i> Daftar Tiket
        </div>
        <div id="ticketsContent">
            <div class="table-responsive" id="ticketsTableWrapper">
                <table class="table table-hover align-middle" id="ticketsTable">
                    <thead class="table-light">
                        <tr>
                            <th>ID Tiket</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Prioritas</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td>{{ $ticket->ticket_id }}</td>
                                <td>{{ $ticket->category->name ?? '-' }}</td>
                                <td>{{ Str::limit($ticket->description, 50) }}</td>
                                <td>
                                    <span class="badge 
                                        @if($ticket->status == 'open') bg-success
                                        @elseif($ticket->status == 'progress') bg-warning text-dark
                                        @elseif($ticket->status == 'resolved') bg-primary
                                        @elseif($ticket->status == 'closed') bg-danger
                                        @else bg-secondary @endif">
                                        {{ ucfirst($ticket->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge 
                                        @if($ticket->priority == 'high') bg-danger
                                        @elseif($ticket->priority == 'medium') bg-warning text-dark
                                        @elseif($ticket->priority == 'low') bg-success
                                        @else bg-secondary @endif">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                </td>
                                <td>{{ $ticket->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info view-ticket-btn"
                                        data-id="{{ $ticket->ticket_id }}"
                                        data-category="{{ $ticket->category->name ?? '-' }}"
                                        data-description="{{ $ticket->description }}"
                                        data-status="{{ ucfirst($ticket->status) }}"
                                        data-priority="{{ ucfirst($ticket->priority) }}"
                                        data-created="{{ $ticket->created_at->format('d M Y H:i') }}"
                                        data-attachments='@json($ticket->attachments)'>
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">Belum ada tiket aktif.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Pagination --}}
            <div class="d-flex justify-content-end mt-0 pagination-wrapper">
                {{ $tickets->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
    {{-- ================== RIWAYAT TIKET ================== --}}
    <div class="tickets-container fade-in mb-5">
        <div class="tickets-header mb-1">
            <i class="fas fa-archive"></i> Riwayat Tiket
        </div>
        <div id="historyTicketsContent">
            <div class="table-responsive" id="historyTicketsTableWrapper">
                <table class="table table-hover align-middle" id="historyTicketsTable">
                    <thead class="table-light">
                        <tr>
                            <th>ID Tiket</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Prioritas</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historyTickets as $ticket)
                            <tr>
                                <td>{{ $ticket->ticket_id }}</td>
                                <td>{{ $ticket->category->name ?? '-' }}</td>
                                <td>{{ Str::limit($ticket->description, 50) }}</td>
                                <td>
                                    <span class="badge 
                                        @if($ticket->status == 'resolved') bg-primary
                                        @elseif($ticket->status == 'closed') bg-danger
                                        @else bg-secondary @endif">
                                        {{ ucfirst($ticket->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge 
                                        @if($ticket->priority == 'high') bg-danger
                                        @elseif($ticket->priority == 'medium') bg-warning text-dark
                                        @elseif($ticket->priority == 'low') bg-success
                                        @else bg-secondary @endif">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                </td>
                                <td>{{ $ticket->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info view-ticket-btn"
                                        data-id="{{ $ticket->ticket_id }}"
                                        data-category="{{ $ticket->category->name ?? '-' }}"
                                        data-description="{{ $ticket->description }}"
                                        data-status="{{ ucfirst($ticket->status) }}"
                                        data-priority="{{ ucfirst($ticket->priority) }}"
                                        data-created="{{ $ticket->created_at->format('d M Y H:i') }}"
                                        data-attachments='@json($ticket->attachments)'>
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">Belum ada riwayat tiket.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- ========================================================================= --}}
{{-- ============================== MODALS =================================== --}}
{{-- ========================================================================= --}}

<div class="modal fade" id="createTicketModal" tabindex="-1" aria-labelledby="createTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <form id="createTicketForm" action="{{ route('staff.tickets.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                {{-- Input tersembunyi ini tidak lagi wajib karena controller sudah handle, tapi boleh ada sebagai fallback --}}
                <input type="hidden" name="priority" value="low">
                <div class="modal-header border-0">
                    <h1 class="modal-title fs-4 fw-bold text-primary" id="createTicketModalLabel">
                        <i class="fas fa-ticket-alt me-2"></i> Buat Tiket Baru
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Nama</label><input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled></div>
                        <div class="col-md-6"><label class="form-label">Departemen</label><input type="text" class="form-control" value="{{ Auth::user()->department ?? 'Belum diatur' }}" disabled></div>
                        <div class="col-md-6">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select" name="category_id" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6"><label class="form-label">Email Kontak</label><input type="email" class="form-control" value="{{ Auth::user()->email }}" disabled></div>
                        <div class="col-12"><label class="form-label">Deskripsi <span class="text-danger">*</span></label><textarea class="form-control" name="description" style="height: 120px" required></textarea></div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-paperclip me-1 text-secondary"></i> Lampiran File
                                <small class="text-muted">(opsional)</small>
                            </label>
                            <input type="file" name="attachments[]" multiple class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Batal</button>
                    <button type="submit" id="submitTicketBtn" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i> Kirim Tiket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="showTicketModal" tabindex="-1" aria-labelledby="showTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="showTicketModalLabel"><i class="fas fa-ticket-alt"></i> Detail Tiket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tr><th width="25%">ID Tiket</th><td id="show-ticket-id"></td></tr>
                    <tr><th>Kategori</th><td id="show-ticket-category"></td></tr>
                    <tr><th>Deskripsi</th><td id="show-ticket-description" style="white-space: pre-wrap;"></td></tr>
                    <tr><th>Status</th><td><span class="badge" id="show-ticket-status"></span></td></tr>
                    <tr><th>Prioritas</th><td><span class="badge" id="show-ticket-priority"></span></td></tr>
                    <tr><th>Dibuat</th><td id="show-ticket-created"></td></tr>
                    <tr id="row-ticket-files" style="display: none;"><th>Lampiran</th><td id="show-ticket-files"></td></tr>
                </table>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="zoomImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark"><img id="zoom-image" src="" class="img-fluid rounded" alt="Zoomed Image"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // =====================================================================
    // HELPER FUNCTIONS
    // =====================================================================
    const getStatusBadgeClass = (status) => {
        if (!status) return 'bg-secondary';
        status = status.toLowerCase();
        if (status === 'open') return 'bg-success';
        if (status === 'progress') return 'bg-warning text-dark';
        if (status === 'resolved') return 'bg-primary';
        if (status === 'closed') return 'bg-danger';
        return 'bg-secondary';
    };

    const getPriorityBadgeClass = (priority) => {
        if (!priority) return 'bg-secondary';
        priority = priority.toLowerCase();
        if (priority === 'high') return 'bg-danger';
        if (priority === 'medium') return 'bg-warning text-dark';
        if (priority === 'low') return 'bg-success';
        return 'bg-secondary';
    };

    // Fungsi untuk escape string
    const escape = (str = '') =>
        String(str).replace(/[&<>"']/g, (m) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[m]));

    // Fungsi terpusat untuk membuat <tr>
    const createTicketRow = (ticket) => {
        const statusClass = getStatusBadgeClass(ticket.status);
        const priorityClass = getPriorityBadgeClass(ticket.priority);
        const description = ticket.description
            ? ticket.description.substring(0, 50) + (ticket.description.length > 50 ? '...' : '')
            : '-';
        const categoryName = ticket.category ? ticket.category.name : '-';

        return `
            <tr>
                <td>${escape(ticket.ticket_id)}</td>
                <td>${escape(categoryName)}</td>
                <td>${escape(description)}</td>
                <td><span class="badge ${statusClass}">${escape(ticket.status)}</span></td>
                <td><span class="badge ${priorityClass}">${escape(ticket.priority)}</span></td>
                <td>${escape(ticket.created_at)}</td>
                <td>
                    <button class="btn btn-sm btn-info view-ticket-btn"
                        data-id="${escape(ticket.ticket_id)}"
                        data-category="${escape(categoryName)}"
                        data-description="${escape(ticket.description)}"
                        data-status="${escape(ticket.status)}"
                        data-priority="${escape(ticket.priority)}"
                        data-created="${escape(ticket.created_at)}"
                        data-attachments='${escape(JSON.stringify(ticket.attachments))}'>
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `;
    };

    // =====================================================================
    // EVENT LISTENERS
    // =====================================================================

    // 1. Submit Form Buat Tiket (AJAX)
    const createTicketForm = document.getElementById("createTicketForm");
    if (createTicketForm) {
        createTicketForm.addEventListener("submit", function(e) {
            e.preventDefault();
            const form = e.target;
            const submitButton = document.getElementById('submitTicketBtn');
            const originalButtonHtml = submitButton.innerHTML;
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Mengirim...`;
            submitButton.disabled = true;

            fetch(form.action, {
                method: "POST",
                headers: { "Accept": "application/json" },
                body: new FormData(form)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const newRowHtml = createTicketRow(data.ticket);
                    const tableBody = document.querySelector("#ticketsTable tbody");
                    const emptyRow = tableBody.querySelector('td[colspan="7"]');
                    if (emptyRow) emptyRow.parentElement.remove();
                    tableBody.insertAdjacentHTML("afterbegin", newRowHtml);
                    form.reset();
                    bootstrap.Modal.getInstance(form.closest('.modal')).hide();
                    alert(data.message);
                } else {
                    let errorMessages = "Gagal membuat tiket:\n" + (data.message || '');
                    if (data.errors) {
                        for (const error in data.errors) {
                            errorMessages += `\n- ${data.errors[error][0]}`;
                        }
                    }
                    alert(errorMessages);
                }
            })
            .catch(err => {
                console.error('Fetch Error:', err);
                alert("Terjadi kesalahan saat mengirim tiket.\n\nDetail: " + err.message);
            })
            .finally(() => {
                submitButton.innerHTML = originalButtonHtml;
                submitButton.disabled = false;

                // 🚀 Apapun yang terjadi, reload halaman setelah 1 detik
                setTimeout(() => location.reload(), 500);
            });
        });
    }

    // 2. Tampilkan Modal Detail Tiket
    function bindViewButtonEvents() {
        const showModal = new bootstrap.Modal(document.getElementById('showTicketModal'));
        document.querySelectorAll('.view-ticket-btn').forEach(button => {
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            newButton.addEventListener('click', function() {
                const ticketData = this.dataset;
                document.getElementById("show-ticket-id").textContent = ticketData.id;
                document.getElementById("show-ticket-category").textContent = ticketData.category;
                document.getElementById("show-ticket-description").textContent = ticketData.description;
                document.getElementById("show-ticket-created").textContent = ticketData.created;

                const statusBadge = document.getElementById("show-ticket-status");
                statusBadge.textContent = ticketData.status;
                statusBadge.className = `badge ${getStatusBadgeClass(ticketData.status)}`;

                const priorityBadge = document.getElementById("show-ticket-priority");
                priorityBadge.textContent = ticketData.priority;
                priorityBadge.className = `badge ${getPriorityBadgeClass(ticketData.priority)}`;

                const attachments = JSON.parse(ticketData.attachments || "[]");
                const rowFiles = document.getElementById("row-ticket-files");
                const filesContainer = document.getElementById("show-ticket-files");
                filesContainer.innerHTML = "";

                if (attachments.length > 0) {
                    rowFiles.style.display = "";
                    attachments.forEach(file => {
                        const fileUrl = `/storage/${file}`;
                        const ext = file.split('.').pop().toLowerCase();
                        if (['jpg','jpeg','png','gif','webp'].includes(ext)) {
                            const img = document.createElement("img");
                            img.src = fileUrl;
                            img.className = "img-thumbnail me-2 mb-2";
                            img.style.cssText = "max-width: 150px; cursor: pointer;";
                            img.onclick = () => {
                                document.getElementById("zoom-image").src = fileUrl;
                                new bootstrap.Modal(document.getElementById("zoomImageModal")).show();
                            };
                            filesContainer.appendChild(img);
                        } else {
                            const link = document.createElement("a");
                            link.href = fileUrl;
                            link.target = "_blank";
                            link.textContent = file.split("/").pop();
                            link.className = "d-block";
                            filesContainer.appendChild(link);
                        }
                    });
                } else {
                    rowFiles.style.display = "none";
                }
                showModal.show();
            });
        });
    }

    // 3. Live Search (AJAX)
    const searchInput = document.getElementById("searchInput");
    if(searchInput) {
        let searchTimer = null;
        searchInput.addEventListener("input", function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                const query = searchInput.value;
                const url = `{{ route('staff.tickets.index') }}?search=${encodeURIComponent(query)}`;
                
                fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest", "Accept": "application/json" } })
                .then(res => res.json())
                .then(data => {
                    const tableBody = document.querySelector("#ticketsTable tbody");
                    const paginationWrapper = document.querySelector('.pagination-wrapper');
                    tableBody.innerHTML = "";
                    
                    if(data.data && data.data.length > 0) {
                        data.data.forEach(ticket => {
                            tableBody.innerHTML += createTicketRow(ticket);
                        });
                    } else {
                        tableBody.innerHTML = `<tr><td colspan="7" class="text-center">Tidak ada tiket yang cocok.</td></tr>`;
                    }
                    
                    paginationWrapper.innerHTML = '';
                    bindViewButtonEvents();
                })
                .catch(err => console.error(err));
            }, 400);
        });
    }

    // Bind pertama kali
    bindViewButtonEvents();
});
</script>

@endpush