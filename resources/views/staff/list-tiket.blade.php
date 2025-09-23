@extends('layouts.staff')

@section('title', 'Daftar Tiket')
@vite(['resources/css/list-tiket.css','resources/js/list-tiket.js'])

@section('content')
<div class="container">
    <!-- Filters Section -->
    <div class="filters-section fade-in">
        <div class="filters-row">
            <form method="GET" action="{{ route('staff.tickets.index') }}" 
                  class="d-flex gap-2 flex-wrap" id="filterForm">

                <!-- Search -->
                <div class="search-box">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        class="search-input" placeholder="Cari tiket..." id="searchInput">
                    <i class="fas fa-search search-icon"></i>
                </div>
            </form>

            <!-- Tombol buat tiket -->
            <button type="button" class="create-btn" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                <i class="fas fa-plus"></i> Buat Tiket Baru
            </button>
        </div>
    </div>

    <!-- Tickets List -->
    <div class="tickets-container fade-in">
        <div class="tickets-header">
            <i class="fas fa-list"></i> Daftar Tiket
        </div>

        <div id="ticketsContent">
            <div class="table-responsive" id="ticketsTableWrapper">
    <table class="table table-hover align-middle" id="ticketsTable">
        <thead>
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
                        @else bg-secondary
                        @endif">
                        {{ ucfirst($ticket->status) }}
                    </span>
                </td>
                <td>
                    <span class="badge 
                        @if($ticket->priority == 'high') bg-danger
                        @elseif($ticket->priority == 'medium') bg-warning text-dark
                        @elseif($ticket->priority == 'low') bg-success
                        @else bg-secondary
                        @endif">
                        {{ ucfirst($ticket->priority) }}
                    </span>
                </td>
                <td>{{ $ticket->created_at->format('d M Y H:i') }}</td>
                <td>
                    <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                        data-bs-target="#showTicketModal"
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
                <td colspan="7" class="text-center">Belum ada tiket.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="pagination">
    <div class="pagination-info">
        Menampilkan {{ $tickets->firstItem() ?? 0 }}-{{ $tickets->lastItem() ?? 0 }} dari
        {{ $tickets->total() ?? 0 }} tiket
    </div>
    <div class="pagination-controls">
        {{ $tickets->appends(request()->query())->links() }}
    </div>
</div>

        </div>
    </div>
</div>

{{-- ✅ Include modal form-ticket --}}
@include('staff.modals.form-ticket')

<!-- Modal Show Ticket -->
<div class="modal fade" id="showTicketModal" tabindex="-1" aria-labelledby="showTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="showTicketModalLabel">
                    <i class="fas fa-ticket-alt"></i> Detail Tiket
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="25%">ID Tiket</th>
                        <td id="show-ticket-id"></td>
                    </tr>
                    <tr>
                        <th>Kategori</th>
                        <td id="show-ticket-category"></td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td id="show-ticket-description"></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><span class="badge bg-secondary" id="show-ticket-status"></span></td>
                    </tr>
                    <tr>
                        <th>Prioritas</th>
                        <td><span class="badge bg-warning text-dark" id="show-ticket-priority"></span></td>
                    </tr>
                    <tr>
                        <th>Dibuat</th>
                        <td id="show-ticket-created"></td>
                    </tr>
                    <tr id="row-ticket-files" style="display: none;">
                        <th>Lampiran</th>
                        <td id="show-ticket-files"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Zoom Gambar -->
<div class="modal fade" id="zoomImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark">
            <img id="zoom-image" src="" class="img-fluid rounded" alt="Zoomed Image">
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const showModal = document.getElementById('showTicketModal');
    const zoomModal = new bootstrap.Modal(document.getElementById('zoomImageModal'));
    const zoomImage = document.getElementById('zoom-image');

    // 🔍 Live Search tanpa reload
    const searchInput = document.getElementById("searchInput");
    const ticketsContent = document.getElementById("ticketsContent");
    let timer = null;

    searchInput.addEventListener("input", function () {
        clearTimeout(timer);
        timer = setTimeout(() => {
            const query = searchInput.value;

            fetch("{{ route('staff.tickets.index') }}?search=" + encodeURIComponent(query), {
                headers: {
                    "X-Requested-With": "XMLHttpRequest" // supaya tahu ini AJAX
                }
            })
            .then(res => res.text())
            .then(html => {
                // Ambil hanya #ticketsContent dari respon
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, "text/html");
                const newContent = doc.getElementById("ticketsContent").innerHTML;
                ticketsContent.innerHTML = newContent;
            })
            .catch(err => console.error(err));
        }, 400); // delay ketik
    });

    // Modal detail tiket (biarkan sama)
    showModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        document.getElementById('show-ticket-id').textContent = button.getAttribute('data-id');
        document.getElementById('show-ticket-category').textContent = button.getAttribute('data-category');
        document.getElementById('show-ticket-description').textContent = button.getAttribute('data-description');
        document.getElementById('show-ticket-status').textContent = button.getAttribute('data-status');
        document.getElementById('show-ticket-priority').textContent = button.getAttribute('data-priority');
        document.getElementById('show-ticket-created').textContent = button.getAttribute('data-created');

        // Lampiran
        let files = [];
        try {
            files = JSON.parse(button.getAttribute('data-attachments') || "[]");
            if (!Array.isArray(files)) files = [files];
        } catch (e) { files = []; }

        const rowFiles = document.getElementById('row-ticket-files');
        const filesContainer = document.getElementById('show-ticket-files');
        filesContainer.innerHTML = "";

        if (files.length > 0) {
            rowFiles.style.display = "";

            files.forEach(file => {
                const ext = file.split('.').pop().toLowerCase();
                if (['jpg','jpeg','png','gif','heif','webp'].includes(ext)) {
                    const img = document.createElement('img');
                    img.src = `/storage/${file}`;
                    img.className = "img-thumbnail me-2 mb-2";
                    img.style.maxWidth = "150px";
                    img.style.cursor = "pointer";
                    img.onclick = function() {
                        zoomImage.src = img.src;
                        zoomModal.show();
                    }
                    filesContainer.appendChild(img);
                } else {
                    const link = document.createElement('a');
                    link.href = `/storage/${file}`;
                    link.target = "_blank";
                    link.textContent = file.split('/').pop();
                    filesContainer.appendChild(link);
                    filesContainer.appendChild(document.createElement('br'));
                }
            });
        } else {
            rowFiles.style.display = "none";
        }
    });
});
</script>

@endsection
