@extends('layouts.it')

@section('title', 'IT Ticket List')
@vite(['resources/css/it.css', 'resources/js/it.js'])

@section('content')
<div class="container py-4">

    <h2 class="fw-bold mb-3"><i class="fas fa-ticket-alt text-primary me-2"></i> IT Ticket List</h2>

    {{-- ================= FILTER ================= --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('it.tickets.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by ID or description..." value="{{ request('search') }}">
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
                        <th class="text-start">Description</th>
                        <th>Department</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Created By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr id="ticket-row-{{ $ticket->id }}" class="priority-{{ $ticket->priority }}">
                        <td data-label="Ticket ID">
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2">#{{ $ticket->ticket_id }}</span>
                        </td>
                        <td data-label="Description" class="text-start">{{ Str::limit($ticket->description, 60) }}</td>
                        {{-- FIX: Ambil department dari tiket, bukan dari user --}}
                        <td data-label="Department">{{ optional($ticket->department)->name ?? '-' }}</td>
                        <td data-label="Category">{{ $ticket->category->name ?? '-' }}</td>
                        <td data-label="Status">
                            @php
                                $statusTextClasses = [
                                    'waiting'     => 'text-secondary', 'in_progress' => 'text-warning-emphasis',
                                    'pending'     => 'text-info-emphasis', 'resolved'    => 'text-success',
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
                                @foreach(['low','medium','high','urgent'] as $priority)
                                <option value="{{ $priority }}" @selected($ticket->priority == $priority)>{{ ucfirst($priority) }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td data-label="Created By">
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-2 rounded-pill">{{ $ticket->user->name ?? '-' }}</span>
                        </td>
                        <td data-label="Action">
                            <button class="btn btn-sm btn-outline-primary btn-detail-ticket" data-id="{{ $ticket->id }}">
                                <i class="fas fa-eye me-1"></i> Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted p-4">No tickets found.</td>
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

{{-- ================= MODAL DETAIL ================= --}}
<div class="modal fade" id="detailTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-primary"><i class="fas fa-ticket-alt me-2"></i> Ticket Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                
                {{-- PASTIKAN ELEMEN INI ADA --}}
                <div id="d_loader" class="text-center my-4">
                    <div class="spinner-border text-primary"></div>
                </div>

                {{-- PASTIKAN ELEMEN INI DAN ISINYA ADA --}}
                <div id="d_content" class="d-none">
                    <table class="table table-borderless">
                        <tr><th width="25%">Ticket ID</th><td><span id="d_ticket_id" class="fw-bold text-primary"></span></td></tr>
                        <tr><th>Created By</th><td><span id="d_user"></span></td></tr>
                        <tr><th>Department</th><td><span id="d_department"></span></td></tr>
                        <tr><th>Category</th><td><span id="d_category"></span></td></tr>
                        <tr><th>Status</th><td><span class="badge" id="d_status"></span></td></tr>
                        <tr><th>Priority</th><td><span class="badge" id="d_priority"></span></td></tr>
                        <tr><th>Description</th><td style="white-space: pre-wrap; text-bold"><span id="d_description"></span></td></tr>
                        <tr><th>Date</th><td><span id="d_created"></span></td></tr>
                        <tr id="d_row_notes" class="d-none">
                            <th class="align-text-top">Resolution Notes</th>
                            <td><div id="d_notes" class="text-muted fst-italic bg-light p-2 rounded"></div></td>
                        </tr>
                        <tr>
                            <th class="align-top">Attachments</th>
                            <td><div id="d_attachments" class="d-flex flex-wrap gap-2"></div></td>
                        </tr>
                    </table>
                </div>

            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ================= RESOLUTION NOTES MODAL ================= --}}
<div class="modal fade" id="resolutionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add Resolution Notes</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Please provide notes for changing the status to Pending/Closed.</p>
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