@extends('layouts.staff')

@section('title', 'Ticket List')
@vite(['resources/css/list-tiket.css','resources/js/list-tiket.js'])

@section('content')
<div class="container my-2">

    <div class="filters-section fade-in small-container mb-4">
    <div class="row g-2 align-items-center">
        <div class="col-12 col-md-8">
            <form method="GET" action="{{ route('staff.tickets.index') }}" id="filterForm">
                <div class="search-box position-relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="search-input form-control pe-5"
                        placeholder="Search tickets..." id="searchInput">
                    <i class="fas fa-search search-icon position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                </div>
            </form>
        </div>
        <div class="col-12 col-md-4 text-md-end">
            <button type="button" class="btn btn-primary w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                <i class="fas fa-plus"></i> Create New Ticket
            </button>
        </div>
    </div>
</div>


    {{-- ================== ACTIVE TICKET LIST ================== --}}
    <div class="tickets-container fade-in mb-5">
        <div class="tickets-header mb-3">
            <i class="fas fa-list"></i> Ticket List
        </div>
        <div id="ticketsContent">
            <div class="table-responsive" id="ticketsTableWrapper">
                <table class="table table-hover align-middle" id="ticketsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Ticket ID</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Created</th>
                            <th>Action</th>
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
                                <td colspan="7" class="text-center py-4">No active tickets found.</td>
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
    {{-- ================== TICKET HISTORY ================== --}}
    <div class="tickets-container fade-in mb-5">
        <div class="tickets-header mb-1">
            <i class="fas fa-archive"></i> Ticket History
        </div>
        <div id="historyTicketsContent">
            <div class="table-responsive" id="historyTicketsTableWrapper">
                <table class="table table-hover align-middle" id="historyTicketsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Ticket ID</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Created</th>
                            <th>Action</th>
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
                                <td colspan="7" class="text-center py-4">No ticket history found.</td>
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
    <div class="modal-dialog modal-lg modal-dialog-centered custom-modal">
        <div class="modal-content shadow-lg border-0">
            <form id="createTicketForm" action="{{ route('staff.tickets.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="priority" value="low">
                <div class="modal-header border-0">
                    <h1 class="modal-title fs-4 fw-bold text-white" id="createTicketModalLabel">
                        <i class="fas fa-ticket-alt me-2"></i> Create New Ticket
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Name</label><input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled></div>
                        <div class="col-md-6"><label class="form-label">Department</label><input type="text" class="form-control" value="{{ Auth::user()->department ?? 'Not set' }}" disabled></div>
                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6"><label class="form-label">Contact Email</label><input type="email" class="form-control" value="{{ Auth::user()->email }}" disabled></div>
                        <div class="col-12"><label class="form-label">Description <span class="text-danger">*</span></label><textarea class="form-control" name="description" style="height: 120px" required></textarea></div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-paperclip me-1 text-secondary"></i> File Attachments
                                <small class="text-muted">(optional)</small>
                            </label>
                            <input type="file" name="attachments[]" multiple class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Cancel</button>
                    <button type="submit" id="submitTicketBtn" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i> Submit Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="showTicketModal" tabindex="-1" aria-labelledby="showTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="showTicketModalLabel"><i class="fas fa-ticket-alt"></i> Ticket Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tr><th width="25%">Ticket ID</th><td id="show-ticket-id"></td></tr>
                    <tr><th>Category</th><td id="show-ticket-category"></td></tr>
                    <tr><th>Description</th><td id="show-ticket-description" style="white-space: pre-wrap;"></td></tr>
                    <tr><th>Status</th><td><span class="badge" id="show-ticket-status"></span></td></tr>
                    <tr><th>Priority</th><td><span class="badge" id="show-ticket-priority"></span></td></tr>
                    <tr><th>Created</th><td id="show-ticket-created"></td></tr>
                    <tr id="row-ticket-files" style="display: none;"><th>Attachments</th><td id="show-ticket-files"></td></tr>
                </table>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
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
    // HELPER FUNCTIONS (Fungsi Bantuan)
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
    const escapeHTML = (str = '') =>
        String(str).replace(/[&<>"']/g, (m) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[m]));

    const createTicketRow = (ticket) => {
        const description = ticket.description
            ? (ticket.description.length > 50 ? ticket.description.substring(0, 50) + '...' : ticket.description)
            : '-';
        return `
            <tr>
                <td data-label="Ticket ID">${escapeHTML(ticket.ticket_id)}</td>
                <td data-label="Category">${escapeHTML(ticket.category.name ?? '-')}</td>
                <td data-label="Description">${escapeHTML(description)}</td>
                <td data-label="Status"><span class="badge ${getStatusBadgeClass(ticket.status)}">${escapeHTML(ticket.status)}</span></td>
                <td data-label="Priority"><span class="badge ${getPriorityBadgeClass(ticket.priority)}">${escapeHTML(ticket.priority)}</span></td>
                <td data-label="Created">${escapeHTML(ticket.created_at_formatted)}</td>
                <td data-label="Action">
                    <button class="btn btn-sm btn-info view-ticket-btn"
                        data-id="${escapeHTML(ticket.ticket_id)}" data-category="${escapeHTML(ticket.category.name ?? '-')}"
                        data-description="${escapeHTML(ticket.description)}" data-status="${escapeHTML(ticket.status)}"
                        data-priority="${escapeHTML(ticket.priority)}" data-created="${escapeHTML(ticket.created_at_formatted)}"
                        data-attachments='${escapeHTML(JSON.stringify(ticket.attachments))}'>
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `;
    };

    // =====================================================================
    // FUNGSI UTAMA UNTUK MENGUPDATE TAMPILAN
    // =====================================================================
    function updateTable(data) {
        const tableBody = document.querySelector("#ticketsTable tbody");
        const paginationWrapper = document.querySelector('.pagination-wrapper');
        tableBody.innerHTML = ""; // Kosongkan tabel

        if (data.data && data.data.length > 0) {
            data.data.forEach(ticket => {
                tableBody.innerHTML += createTicketRow(ticket);
            });
        } else {
            tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4">No matching tickets found.</td></tr>`;
        }
        
        paginationWrapper.innerHTML = data.links; // Update link pagination
        bindViewButtonEvents(); // Pasang ulang event listener untuk tombol "View"
    }
    
    // =====================================================================
    // EVENT LISTENERS
    // =====================================================================

    // 1. Fungsi untuk Modal Detail Tiket
    function bindViewButtonEvents() {
        const showModalEl = document.getElementById('showTicketModal');
        if (!showModalEl) return;
        const showModal = new bootstrap.Modal(showModalEl);
        
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
                        if (['jpg','jpeg','png','gif','webp','heif'].includes(ext)) {
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

    // 2. Fungsi untuk Live Search
    const searchInput = document.getElementById("searchInput");
    if (searchInput) {
        let searchTimer = null;
        searchInput.addEventListener("input", function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                const query = searchInput.value;
                const url = `{{ route('staff.tickets.index') }}?search=${encodeURIComponent(query)}`;
                
                fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest", "Accept": "application/json" } })
                .then(res => res.json())
                .then(data => updateTable(data))
                .catch(err => console.error(err));
            }, 400);
        });
    }

    // 3. Fungsi untuk Pagination AJAX
    const paginationWrapper = document.querySelector('.pagination-wrapper');
    if (paginationWrapper) {
        paginationWrapper.addEventListener('click', function(event) {
            if (event.target.tagName === 'A' && event.target.classList.contains('page-link')) {
                event.preventDefault();
                const url = event.target.getAttribute('href');
                
                fetch(url, { headers: { "X-Requested-With": "XMLHttpRequest", "Accept": "application/json" } })
                .then(res => res.json())
                .then(data => updateTable(data))
                .catch(err => console.error(err));
            }
        });
    }

    // Panggil fungsi ini saat halaman pertama kali dimuat
    bindViewButtonEvents();
});
</script>
@endpush