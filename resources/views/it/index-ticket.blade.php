@extends('layouts.it')

@section('title', 'IT Ticket List')

@section('content')
{{-- FIXED: Hapus container atau ganti dengan container-fluid --}}
@push('styles')
    @vite('resources/css/it-ticket-index.css')
@endpush
<div class="py-1">

    <h2 class="fw-bold mb-2"><i class="fas fa-ticket-alt text-primary me-2"></i> IT Ticket List</h2>

    {{-- ================= FILTER ================= --}}
    <div class="card mb-2 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('it.tickets.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by ID or Problem..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach(['waiting','in_progress','pending','resolved'] as $status)
                        <option value="{{ $status }}" @selected(request('status')==$status)>{{ ucfirst(str_replace('_',' ',$status)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category_id')==$cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary"><i class="fas fa-filter me-1"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= TICKET TABLE ================= --}}
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>Ticket ID</th>
                        <th>Name</th>
                        <th class="text-start">Problem</th>
                        <th>Category</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Report Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr id="ticket-row-{{ $ticket->id }}" class="priority-{{ $ticket->priority }}">
                        <td data-label="Ticket ID">
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2">#{{ $ticket->ticket_id }}</span>
                        </td>
                        
                        <td data-label="Created By">
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-2">{{ $ticket->user->name ?? '-' }}</span>
                        </td>
                        <td data-label="Description" class="text-start">{{ Str::limit($ticket->description, 60) }}</td>
                        <td data-label="Category">{{ $ticket->category->name ?? '-' }}</td>
                        <td data-label="Department">{{ optional($ticket->department)->name ?? '-' }}</td>
                        <td data-label="Status">
                            @php
                                $statusTextClasses = [
                                    'waiting'     => 'text-secondary', 
                                    'in_progress' => 'text-warning-emphasis',
                                    'pending'     => 'text-info-emphasis', 
                                    'resolved'    => 'text-success',
                                    'closed'      => 'text-danger',
                                ];
                            @endphp
                            <select class="form-select form-select-sm update-ticket-field fw-bold {{ $statusTextClasses[$ticket->status] ?? 'text-dark' }}" data-id="{{ $ticket->id }}" data-field="status" data-original-value="{{ $ticket->status }}">
                                @foreach(['waiting','in_progress','pending','resolved','closed'] as $status)
                                <option value="{{ $status }}" @selected($ticket->status == $status)>{{ ucfirst(str_replace('_',' ',$status)) }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td data-label="Priority">
                            <select class="form-select form-select-sm update-ticket-field select-priority-{{$ticket->priority}}" data-id="{{ $ticket->id }}" data-field="priority" data-original-value="{{ $ticket->priority }}">
                                @foreach(['low','medium','high','urgent','critical'] as $priority)
                                <option value="{{ $priority }}" @selected($ticket->priority == $priority)>{{ ucfirst($priority) }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td data-label="Report Date">
                            <small class="text-muted">
                                {{ $ticket->created_at->format('d M Y H:i') }}
                            </small>
                        </td>
                        <td data-label="Action">
                            <button class="btn btn-sm btn-outline-primary btn-detail-ticket" data-id="{{ $ticket->id }}">
                                <i class="fas fa-eye me-1"></i> Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted p-4">No tickets found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <span>Showing {{ $tickets->firstItem() ?? 0 }} to {{ $tickets->lastItem() ?? 0 }} of {{ $tickets->total() }}</span>
            {{ $tickets->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

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
                                            <i class="me-1"></i>Remark
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
                <h5 class="modal-title">Add Remark</h5>
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

@endsection