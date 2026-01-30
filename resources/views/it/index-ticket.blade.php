@extends('layouts.it')

@section('title', 'IT Ticket List')

@section('content')
{{-- FIXED: Hapus container atau ganti dengan container-fluid --}}
@push('styles')
    @vite('resources/css/it-ticket-index.css')
@endpush
<div class="py-0">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-0">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fas fa-ticket-alt text-primary me-2"></i>IT Ticket Dashboard
            </h4>
            <p class="text-muted small mb-0">Manage and track support requests efficiently.</p>
        </div>
        <button class="btn btn-primary px-4 shadow-sm mt-3 mt-md-0 rounded-pill" data-bs-toggle="modal" data-bs-target="#createTicketModal">
            <i class="fas fa-plus me-2"></i>Create Ticket
        </button>
    </div>

    <!-- Filter Section (Simplified) -->
    <div class="bg-white p-3 rounded-4 shadow-sm mb-4">
        <form method="GET" action="{{ route('it.tickets.index') }}" class="row g-3 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search ticket ID, user, or problem..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select text-muted" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    @foreach(['waiting','in_progress','pending','resolved'] as $status)
                    <option value="{{ $status }}" @selected(request('status')==$status)>{{ ucfirst(str_replace('_',' ',$status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select text-muted" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category_id')==$cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 text-end">
                <a href="{{ route('it.tickets.index') }}" class="btn btn-light text-muted w-100" title="Reset Filter"><i class="fas fa-redo"></i></a>
            </div>
        </form>
    </div>

    <!-- Ticket Table Section -->
    <div class="bg-white rounded-4 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary small text-uppercase">
                    <tr>
                        <th class="py-3 ps-4 border-0 rounded-start-4">Ticket</th>
                        <th class="py-3 border-0">Requester</th>
                        <th class="py-3 border-0">Issue</th>
                        <th class="py-3 border-0">Status</th>
                        <th class="py-3 border-0">Priority</th>
                        <th class="py-3 border-0">Date</th>
                        <th class="py-3 pe-4 text-end border-0 rounded-end-4">Action</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($tickets as $ticket)
                    @php
                        $priorityColors = [
                            'low' => '#6c757d',      // Secondary
                            'medium' => '#0d6efd',   // Primary
                            'high' => '#ffc107',     // Warning
                            'urgent' => '#fd7e14',   // Orange
                            'critical' => '#dc3545'  // Danger
                        ];
                        $rowBorderColor = $priorityColors[$ticket->priority] ?? '#6c757d';
                    @endphp
                    <tr class="ticket-row transition-hover" style="border-left: 5px solid {{ $rowBorderColor }};">
                        <td class="ps-3">
                            <span class="text-dark fw-bold">#{{ $ticket->ticket_id }}</span>
                        </td>
                        <td>
                            <div>
                                <h6 class="mb-0 small fw-semibold text-dark">{{ $ticket->user->name ?? 'Unknown' }}</h6>
                                <span class="text-muted extra-small" style="font-size: 11px;">{{ optional($ticket->department)->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td style="max-width: 250px;">
                            <span class="d-block text-truncate text-secondary" style="font-size: 0.95rem;">
                                {{ $ticket->description }}
                            </span>
                            <small class="text-muted">{{ $ticket->category->name ?? '-' }}</small>
                        </td>
                        <td>
                        <td>
                            @php
                                $statusClasses = [
                                    'waiting' => 'text-secondary bg-light',
                                    'in_progress' => 'text-warning-emphasis bg-warning-subtle',
                                    'pending' => 'text-info-emphasis bg-info-subtle',
                                    'resolved' => 'text-success bg-success-subtle',
                                    'closed' => 'text-danger bg-danger-subtle'
                                ];
                                $currStatusClass = $statusClasses[$ticket->status] ?? 'text-secondary bg-light';
                            @endphp
                            <select class="form-select form-select-sm border-0 shadow-none fw-bold {{ $currStatusClass }} update-ticket-field px-3" 
                                    style="width: auto; cursor: pointer; border-radius: 20px;" 
                                    data-id="{{ $ticket->id }}" data-field="status" data-original-value="{{ $ticket->status }}">
                                @foreach(['waiting','in_progress','pending','resolved','closed'] as $s)
                                <option value="{{ $s }}" @selected($ticket->status == $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-select form-select-sm border-0 shadow-none select-priority-{{$ticket->priority}} update-ticket-field" 
                                    style="width: 100px; cursor: pointer;"
                                    data-id="{{ $ticket->id }}" data-field="priority" data-original-value="{{ $ticket->priority }}">
                                @foreach(['low','medium','high','urgent','critical'] as $priority)
                                <option value="{{ $priority }}" @selected($ticket->priority == $priority)>{{ ucfirst($priority) }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-muted small">
                            {{ $ticket->created_at->format('d M, H:i') }}
                        </td>
                        <td class="pe-4 text-end">
                            <button class="btn btn-icon btn-light text-primary rounded-circle btn-detail-ticket" data-id="{{ $ticket->id }}" title="View Details">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">No active tickets found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($tickets->hasPages())
        <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center bg-light bg-opacity-10">
            <small class="text-muted">Showing {{ $tickets->firstItem() }}-{{ $tickets->lastItem() }} of {{ $tickets->total() }}</small>
            <div>{{ $tickets->links('pagination::simple-bootstrap-5') }}</div>
        </div>
        @endif
    </div>
</div>

<style>
/* Custom Tweaks for "Simpler Modern" look */
.table-hover tbody tr:hover {
    background-color: #f8fafc;
}
.transition-hover {
    transition: background-color 0.2s ease;
}
.btn-icon {
    width: 36px; height: 36px;
    display: inline-flex; align-items: center; justify-content: center;
    transition: all 0.2s;
}
.btn-icon:hover {
    background-color: #e0e7ff; /* indigo-100 */
    transform: translateX(3px);
}
.extra-small { font-size: 0.75rem; }
.rounded-4 { border-radius: 1rem !important; }
.form-select:focus { box-shadow: none; border-color: #cbd5e1; }
.avatar-initial { font-family: 'Poppins', sans-serif; }
</style>

{{-- ✅ MODAL DETAIL WITH TIMELINE - UPDATED WITH ICONS --}}
<div class="modal fade" id="detailTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered custom-modal">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-ticket-alt me-2"></i>Ticket Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                
                {{-- Loader --}}
                <div id="d_loader" class="text-center py-4">
                    <div class="spinner-border text-info"></div>
                    <p class="text-muted mt-2">
                        <i class="fas fa-sync-alt fa-spin me-1"></i>Loading ticket...
                    </p>
                </div>

                {{-- Content with Timeline --}}
                <div id="d_content" class="d-none">
                    <div class="row">
                        {{-- LEFT COLUMN: Ticket Info --}}
                        <div class="col-md-7 mb-4 mb-md-0">
                            <div class="ticket-info-section">
                                <table class="table table-borderless mb-0 ticket-detail-table">
                                    <tr>
                                        <th width="35%" class="text-muted">
                                            <i class="me-1"></i>Ticket ID
                                        </th>
                                        <td>
                                            <span id="d_ticket_id" class="fw-bold text-primary">
                                                <i class="me-1"></i>Not Available
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">
                                            <i class="me-1"></i>Name
                                        </th>
                                        <td id="d_user" class="text-secondary">
                                            <i class="me-1"></i>Unknown User
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">
                                            <i class="me-1"></i>Department
                                        </th>
                                        <td id="d_department" class="text-secondary">
                                            <i class="me-1"></i>Not Specified
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">
                                            <i class="me-1"></i>Category
                                        </th>
                                        <td id="d_category" class="text-secondary">
                                            <i class="me-1"></i>Not Specified
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">
                                            <i class="me-1"></i>Status
                                        </th>
                                        <td>
                                            <span id="d_status" class="badge rounded-pill px-3 py-2 bg-secondary">
                                                <i class="me-1"></i>Unknown
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">
                                            <i class="me-1"></i>Priority
                                        </th>
                                        <td>
                                            <span id="d_priority" class="badge rounded-pill px-3 py-2 bg-secondary">
                                                <i class="me-1"></i>Unknown
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted align-top">
                                            <i class="me-1"></i>Problem
                                        </th>
                                        <td id="d_description" class="text-secondary" style="white-space: pre-wrap;">
                                            <i class="me-1"></i>No description provided
                                        </td>
                                    </tr>
                                    <tr id="d_row_notes" class="d-none">
                                        <th class="text-muted align-top">
                                            <i class="me-1"></i>Solution
                                        </th>
                                        <td>
                                            <div id="d_notes" class="text-muted fst-italic bg-light p-2 rounded border">
                                                <i class="me-1"></i>No notes available
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted align-top">
                                            <i class="me-1"></i>Attachments
                                        </th>
                                        <td id="d_attachments">
                                            <span class="text-muted">
                                                <i class="fas fa-paperclip me-1"></i>No attachments
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        {{-- RIGHT COLUMN: Timeline --}}
                        <div class="col-md-5">
                            <div class="card bg-light border-0 timeline-card">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3 timeline-header">
                                        <i class="fas fa-history me-2 text-info"></i>Timeline
                                    </h6>
                                    
                                    {{-- Timeline Container --}}
                                    <div class="timeline-container">
                                        {{-- REPORTED --}}
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-primary">
                                                <i class="fas fa-flag"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <div class="timeline-title text-primary fw-bold">
                                                    <i class="me-1"></i>Reported
                                                </div>
                                                <div class="timeline-date text-muted small" id="d_created">
                                                    <i class="fas fa-calendar-times me-1"></i>Not recorded
                                                </div>
                                            </div>
                                        </div>

                                        {{-- RESPONSE --}}
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-warning" id="d_response_marker">
                                                <i class="fas fa-reply"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <div class="timeline-title text-warning fw-bold">
                                                    <i class="me-1"></i>Response
                                                </div>
                                                <div class="timeline-date text-muted small" id="d_response">
                                                    <i class="fas fa-hourglass-me-1"></i>Waiting for response
                                                </div>
                                            </div>
                                        </div>

                                        {{-- RESOLVED/CLOSED --}}
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-muted" id="d_resolved_marker">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <div class="timeline-title fw-bold" id="d_resolved_title" style="color: #6c757d;">
                                                    <i class="me-1"></i>Not Yet Resolved/Closed
                                                </div>
                                                <div class="timeline-date text-muted small" id="d_resolved">
                                                    <i class="fas fa-hourglass-half me-1"></i>Pending
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ================= RESOLUTION NOTES MODAL ================= --}}
<div class="modal fade" id="resolutionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add Resolution</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Please provide notes for changing the status to Pending/Resolved/Closed.</p>
                <textarea id="resolutionNotes" class="form-control" rows="4"
                    placeholder="Write ticket completion notes..."></textarea>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="saveResolutionBtn" class="btn btn-primary">Save Update</button>
            </div>
        </div>
    </div>
</div>

{{-- ================= CREATE TICKET MODAL ================= --}}
<div class="modal fade" id="createTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-plus-circle me-2"></i>Create Ticket for User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="createTicketForm" enctype="multipart/form-data">
                <div class="modal-body p-4">

                    <div class="row g-3">
                        {{-- Select User --}}
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-uppercase text-muted">Select User</label>
                            <select name="user_id" class="form-select" required>
                                <option value="" selected disabled>-- Choose User --</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ optional($u->department)->name ?? 'No Dept' }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Department (Auto-filled visually, but backend handles it) --}}
                        {{-- Category --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="" selected disabled>-- Choose Category --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Priority --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Priority</label>
                            <select name="priority" class="form-select" required>
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase text-muted">Issue Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Describe the problem..." required></textarea>
                        </div>

                        {{-- Attachments --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase text-muted">Attachments (Optional)</label>
                            <input type="file" name="attachments[]" class="form-control" multiple accept="image/*">
                            <div class="form-text small">Max 5MB per file. Formats: JPG, PNG, HEIC.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="fas fa-save me-1"></i> Create Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // EXISTING: Detail Ticket Logic is in it-ticket-index.js/admin-....js
    // We add Create Ticket Logic here
    
    const createTicketForm = document.getElementById('createTicketForm');
    if (createTicketForm) {
        createTicketForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Creating...';
            btn.disabled = true;

            const formData = new FormData(this);

            fetch('{{ route("it.tickets.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message (using SweetAlert if available or Alert)
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Ticket created successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                             window.location.reload();
                        });
                    } else {
                        alert('Ticket created successfully!');
                        window.location.reload();
                    }
                } else {
                    const msg = data.message || 'Unknown error';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', msg, 'error');
                    } else {
                        alert('Error: ' + msg);
                    }
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'An error occurred. Please try again.', 'error');
                } else {
                    alert('An error occurred. Please try again.');
                }
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
    }
});
</script>
@endpush