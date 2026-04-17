@extends('layouts.it')

@section('title', 'Ticket History')


@section('content')
@php
if (!function_exists('remove_filter_url')) {
    function remove_filter_url($filterName) {
        $currentUrl = request()->fullUrl();
        $url = preg_replace('/([?&])'.$filterName.'=[^&]+(&|$)/', '$1', $currentUrl);
        $url = rtrim($url, '?&');
        return $url;
    }
}
@endphp
@push('styles')
    @vite('resources/css/it-ticket-history.css')
@endpush
@push('scripts')
    @vite('resources/js/it-ticket-history.js')
@endpush
<div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title">
            <i class="fas fa-history me-2 text-primary"></i> IT Ticket History
        </h2>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form id="filterForm" method="GET" action="{{ route('it.tickets.history') }}">
                <div class="row g-3">
                    <!-- Search Input -->
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="Search by description, ticket ID, or name..." 
                                   value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Date Range -->
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="{{ request('start_date') }}">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="{{ request('end_date') }}">
                    </div>
                    
                    <!-- Category Filter -->
                    <div class="col-md-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Priority Filter -->
                    <div class="col-md-3">
                        <label for="priority" class="form-label">Priority</label>
                        <select class="form-select" id="priority" name="priority">
                            <option value="">All Priorities</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="critical" {{ request('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="d-grid gap-2 w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i> Apply Filters
                            </button>
                            <a href="{{ route('it.tickets.history') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Active Filters -->
    @if(request()->hasAny(['search', 'start_date', 'end_date', 'category', 'priority']))
    <div class="mb-3">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <span class="text-muted">Active filters:</span>
            @if(request('search'))
                <span class="badge bg-light text-dark">
                    Search: "{{ request('search') }}" 
                    <a href="{{ remove_filter_url('search') }}" class="remove-filter ms-1">×</a>
                </span>
            @endif
            @if(request('start_date'))
                <span class="badge bg-light text-dark">
                    From: {{ request('start_date') }}
                    <a href="{{ remove_filter_url('start_date') }}" class="remove-filter ms-1">×</a>
                </span>
            @endif
            @if(request('end_date'))
                <span class="badge bg-light text-dark">
                    To: {{ request('end_date') }}
                    <a href="{{ remove_filter_url('end_date') }}" class="remove-filter ms-1">×</a>
                </span>
            @endif
            @if(request('category'))
                @php
                    $categoryName = $categories->where('id', request('category'))->first()->name ?? 'Unknown';
                @endphp
                <span class="badge bg-light text-dark">
                    Category: {{ $categoryName }}
                    <a href="{{ remove_filter_url('category') }}" class="remove-filter ms-1">×</a>
                </span>
            @endif
            @if(request('priority'))
                <span class="badge bg-light text-dark">
                    Priority: {{ ucfirst(request('priority')) }}
                    <a href="{{ remove_filter_url('priority') }}" class="remove-filter ms-1">×</a>
                </span>
            @endif
        </div>
    </div>
    @endif

    <!-- Table Tickets -->
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <!-- Results Summary -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="mb-0 text-muted">
                    Showing {{ $tickets->firstItem() ?? 0 }} - {{ $tickets->lastItem() ?? 0 }} of {{ $tickets->total() }} tickets
                </p>
            </div>
            
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Ticket ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Department</th>
                        <th>Solved/Closed</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="riwayatTbody">
                @forelse($tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->ticket_id }}</td>
                        <td>{{ $ticket->user->name ?? 'Unknown' }}</td>
                        <td>{{ Str::limit($ticket->description, 50) }}</td>
                        <td>{{ $ticket->category->name ?? '-' }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ str_replace('Resolved', 'Solved', ucfirst($ticket->status)) }}</span>
                        </td>
                        <td>
                            @php
                                $priorityColors = [
                                    'low' => 'bg-info',
                                    'medium' => 'bg-primary',
                                    'high' => 'bg-warning',
                                    'urgent' => 'bg-danger',
                                    'critical' => 'bg-dark'
                                ];
                                $priorityColor = $priorityColors[$ticket->priority] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $priorityColor }}">{{ ucfirst($ticket->priority) }}</span>
                        </td>
                        <td>{{ $ticket->department->name ?? '-' }}</td>
<td>{{ $ticket->resolved_at ? $ticket->resolved_at->format('d M Y H:i') : ($ticket->updated_at ? $ticket->updated_at->format('d M Y H:i') : '-') }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary btn-detail-ticket" data-id="{{ $ticket->id }}" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-3 d-block"></i>
                            @if(request()->hasAny(['search', 'start_date', 'end_date', 'category', 'priority']))
                                No tickets found matching your criteria
                            @else
                                No closed tickets available
                            @endif
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $tickets->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

@include('it.partials.ticket-detail-modal')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ============================================
    // TICKET DETAIL MODAL HANDLER FOR HISTORY PAGE
    // ============================================

    function formatEmptyData(value, defaultText = 'Not Available') {
        if (!value || value === '-' || value === 'N/A' || value === '' || value === null) {
            return defaultText;
        }
        return value;
    }

    function renderAttachments(attachments) {
        try {
            if (!attachments || !Array.isArray(attachments) || attachments.length === 0) {
                return '<span class="text-muted"><i class="fas fa-paperclip me-1"></i>No attachments</span>';
            }
            return attachments.map(file => {
                const fileUrl = `/storage/${file}`;
                const fileName = file.split('/').pop();
                const isImage = /\.(jpg|jpeg|png|gif|webp|heic|heif)$/i.test(fileName);
                return isImage
                    ? `<a href="${fileUrl}" target="_blank"><img src="${fileUrl}" alt="Attachment" class="img-thumbnail" style="max-height:100px;"></a>`
                    : `<a href="${fileUrl}" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="fas fa-paperclip me-1"></i> ${fileName}</a>`;
            }).join('');
        } catch (err) {
            console.error('Attachment parse error:', err);
            return '<span class="text-danger">Error loading attachments</span>';
        }
    }

    document.addEventListener('click', async (e) => {
        if (e.target.closest('.btn-detail-ticket')) {
            const button = e.target.closest('.btn-detail-ticket');
            const ticketId = button.dataset.id;
            if (ticketId) {
                await showTicketDetail(ticketId);
            }
        }
    });

    async function showTicketDetail(ticketId) {
        const modalEl = document.getElementById('detailTicketModal');
        if (!modalEl) return;

        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        const loader = document.getElementById('d_loader');
        const content = document.getElementById('d_content');

        if (!loader || !content) return;

        loader.classList.remove('d-none');
        content.classList.add('d-none');
        modal.show();

        try {
            const response = await fetch(`/it/tickets/${ticketId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error(`Failed to fetch: ${response.status}`);
            const ticket = await response.json();

            // Populate basic info
            const updates = {
                'd_ticket_id': formatEmptyData(ticket.ticket_id),
                'd_user': formatEmptyData(ticket.user?.name, 'Unknown User'),
                'd_location': formatEmptyData(ticket.user?.location, 'Unknown Location'),
                'd_department': formatEmptyData(ticket.department?.name, 'Not Specified'),
                'd_category': formatEmptyData(ticket.category?.name, 'Not Specified'),
                'd_description': formatEmptyData(ticket.description, 'No description provided')
            };
            Object.entries(updates).forEach(([id, val]) => {
                const el = document.getElementById(id);
                if (el) el.innerHTML = val;
            });

            // Transferred badge
            const transferredBadge = document.getElementById('d_transferred_badge');
            if (transferredBadge) {
                transferredBadge.innerHTML = (ticket.transfer_logs && ticket.transfer_logs.length > 0)
                    ? '<span class="badge bg-warning text-dark">Transferred</span>'
                    : '';
            }

            // Timeline: Created
            const createdEl = document.getElementById('d_created');
            if (createdEl) {
                const created = ticket.created_at_formatted || ticket.created_at;
                createdEl.innerHTML = (created && created !== 'N/A')
                    ? `<i class="fas fa-calendar-check me-1"></i>${created}`
                    : '<i class="fas fa-calendar-times me-1"></i>Not recorded';
            }

            // Timeline: Response
            const responseEl = document.getElementById('d_response');
            const responseMarker = document.getElementById('d_response_marker');
            if (responseEl && responseMarker) {
                const resp = ticket.response_at_formatted || ticket.updated_at;
                if (resp && resp !== 'Not yet' && resp !== 'N/A' && resp !== 'Not yet responded') {
                    responseEl.innerHTML = `<i class="fas fa-clock me-1"></i>${resp}`;
                    responseMarker.classList.remove('bg-muted');
                    responseMarker.classList.add('bg-warning');
                } else {
                    responseEl.innerHTML = '<i class="fas fa-hourglass-half me-1"></i>Waiting for response';
                    responseMarker.classList.remove('bg-warning');
                    responseMarker.classList.add('bg-muted');
                }
            }

            // Timeline: Pending
            const pendingEl = document.getElementById('d_pending');
            const pendingMarker = document.getElementById('d_pending_marker');
            const timelinePending = document.getElementById('d_timeline_pending');
            if (pendingEl && pendingMarker) {
                const pend = ticket.pending_at_formatted;
                if (pend && pend !== 'Not yet pending' && pend !== 'N/A' && pend !== '-') {
                    if (timelinePending) timelinePending.classList.remove('d-none');
                    pendingEl.innerHTML = `<i class="fas fa-clock me-1"></i>${pend}`;
                    pendingMarker.classList.remove('bg-muted');
                    pendingMarker.classList.add('bg-warning');
                } else {
                    if (timelinePending) timelinePending.classList.add('d-none');
                }
            }

            // Timeline: Transfers
            const timelineTransfers = document.getElementById('d_timeline_transfers');
            if (timelineTransfers) {
                timelineTransfers.innerHTML = '';
                if (ticket.transfer_logs && ticket.transfer_logs.length > 0) {
                    ticket.transfer_logs.forEach(log => {
                        timelineTransfers.insertAdjacentHTML('beforeend', `
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"><i class="fas fa-exchange-alt"></i></div>
                                <div class="timeline-content">
                                    <div class="timeline-title text-info fw-bold">Transferred</div>
                                    <div class="text-dark small mb-1">
                                        <strong>${log.from}</strong> <i class="fas fa-arrow-right mx-1 text-muted"></i> <strong>${log.to}</strong>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                }
            }

            // Timeline: Resolved
            const resolvedEl = document.getElementById('d_resolved');
            const resolvedMarker = document.getElementById('d_resolved_marker');
            const resolvedTitle = document.getElementById('d_resolved_title');
            if (resolvedEl && resolvedMarker && resolvedTitle) {
                if (ticket.resolved_at_formatted && ticket.resolved_at_formatted !== 'Pending' && ticket.resolved_at_formatted !== '-' && ticket.resolved_at_formatted !== 'Not yet resolved') {
                    resolvedEl.innerHTML = `<i class="fas fa-check-double me-1"></i>${ticket.resolved_at_formatted}`;
                    resolvedMarker.classList.remove('bg-muted');
                    resolvedMarker.classList.add('bg-success');
                    resolvedTitle.textContent = ticket.status === 'closed' ? 'Closed' : 'Solved';
                    resolvedTitle.style.color = '#198754';
                } else {
                    resolvedEl.innerHTML = '<i class="fas fa-hourglass-half me-1"></i>Pending';
                    resolvedMarker.classList.remove('bg-success');
                    resolvedMarker.classList.add('bg-muted');
                    resolvedTitle.textContent = 'Not Yet Solved/Closed';
                    resolvedTitle.style.color = '#6c757d';
                }
            }

            // Resolution notes
            const notesRow = document.getElementById('d_row_notes');
            const notesElement = document.getElementById('d_notes');
            if (notesRow && notesElement) {
                if (ticket.resolution_notes) {
                    const tempDiv = document.createElement('div');
                    tempDiv.textContent = ticket.resolution_notes;
                    let safeNotes = tempDiv.innerHTML;
                    
                    const linkifiedNotes = safeNotes.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" class="text-primary text-decoration-underline" rel="noopener noreferrer">$1</a>');
                    notesElement.innerHTML = linkifiedNotes;
                    notesRow.classList.remove('d-none');
                } else {
                    notesRow.classList.add('d-none');
                }
            }

            // Pending reason
            const pendingNotesRow = document.getElementById('d_row_pending_notes');
            const pendingNotesElement = document.getElementById('d_pending_notes');
            if (pendingNotesRow && pendingNotesElement) {
                if (ticket.pending_reason) {
                    pendingNotesElement.textContent = ticket.pending_reason;
                    pendingNotesRow.classList.remove('d-none');
                } else {
                    pendingNotesRow.classList.add('d-none');
                }
            }

            // Transfer history info
            const transfersRow = document.getElementById('d_row_transfers');
            const transfersElement = document.getElementById('d_transfers');
            if (transfersRow && transfersElement) {
                if (ticket.transfer_logs && ticket.transfer_logs.length > 0) {
                    transfersElement.innerHTML = ticket.transfer_logs.map(log =>
                        `<div class="mb-2 p-2 bg-light border rounded">
                            <div class="fw-bold"><i class="fas fa-exchange-alt me-1 text-primary"></i> ${log.from} &rarr; ${log.to}</div>
                            ${log.note ? `<div class="text-muted small fst-italic mb-1"><i class="fas fa-quote-left me-1" style="font-size: 0.8em;"></i>${log.note}</div>` : ''}
                            <div class="text-muted" style="font-size: 0.85em;">
                                <i class="fas fa-user-clock me-1"></i> by ${log.by} on ${log.date}
                            </div>
                         </div>`
                    ).join('');
                    transfersRow.classList.remove('d-none');
                } else {
                    transfersRow.classList.add('d-none');
                }
            }

            // Attachments
            const attachmentsContainer = document.getElementById('d_attachments');
            if (attachmentsContainer) {
                attachmentsContainer.innerHTML = renderAttachments(ticket.attachments || []);
            }

            loader.classList.add('d-none');
            content.classList.remove('d-none');

        } catch (error) {
            console.error('Error loading ticket details:', error);
            loader.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Failed to load ticket details: ${error.message}
                </div>
            `;
        }
    }

    // Reset modal on hide
    const detailModal = document.getElementById('detailTicketModal');
    if (detailModal) {
        detailModal.addEventListener('hidden.bs.modal', function () {
            const loader = document.getElementById('d_loader');
            const content = document.getElementById('d_content');
            if (loader) {
                loader.classList.remove('d-none');
                loader.innerHTML = '<div class="spinner-border text-info"></div><p class="text-muted mt-2"><i class="fas fa-sync-alt fa-spin me-1"></i>Loading ticket without sync...</p>';
            }
            if (content) content.classList.add('d-none');

            // Force cleanup stuck backdrops
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        });
    }
});
</script>
@endpush

@endsection