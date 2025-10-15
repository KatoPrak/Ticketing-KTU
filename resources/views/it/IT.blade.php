@extends('layouts.it')

@section('title', 'Dashboard - IT Team')

@section('content')
<div class="welcome-card mb-4">
    <div class="row align-items-center">
        <div class="col-md-8 col-12">
            <h2><i class="fas fa-wave-square me-2"></i>Welcome! {{ Auth::user()->name }}</h2>
            <p class="mb-0">
                {{ optional(Auth::user()->department)->name ?? '-' }} (ID-{{ Auth::user()->id_staff }})
            </p>
        </div>
        <div class="col-md-4 col-12 text-center text-md-end mt-3 mt-md-0">
            <div class="fs-1">
                <i class="fas fa-user-tie"></i>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-3 col-md-6 col-6">
        <div class="dashboard-card stat-card p-3">
            <div class="stat-number fs-4 fw-bold">{{ $activeTickets ?? 0 }}</div>
            <div class="stat-label small text-muted">Active Tickets</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-6">
        <div class="dashboard-card stat-card p-3">
            <div class="stat-number fs-4 fw-bold">{{ $pendingTickets ?? 0 }}</div>
            <div class="stat-label small text-muted">Pending</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-6">
        <div class="dashboard-card stat-card p-3">
            <div class="stat-number fs-4 fw-bold">{{ $completedTickets ?? 0 }}</div>
            <div class="stat-label small text-muted">Completed</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-6">
        <div class="dashboard-card stat-card p-3">
            <div class="stat-number fs-4 fw-bold">{{ $urgentTickets ?? 0 }}</div>
            <div class="stat-label small text-muted">Urgent</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-12">
        <div class="dashboard-card p-3">
            <h5 class="mb-3"><i class="fas fa-chart-line me-2"></i>Recent Activity</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>Department</th>
                            <th>Subject</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTickets as $ticket)
                            <tr>
                                <td><strong>{{ $ticket->ticket_id ?? ('#'.$ticket->id) }}</strong></td>
                                <td>{{ $ticket->department->name ?? '-' }}</td>
                                <td>{{ $ticket->category->name ?? 'General' }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($ticket->description, 30) }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst(str_replace('_',' ', $ticket->status)) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ ucfirst($ticket->priority) }}</span>
                                </td>
                                <td>{{ optional($ticket->created_at)->format('Y-m-d H:i') }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-detail-ticket"
                                        data-id="{{ $ticket->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No recent ticket activity
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-12">
        <div class="dashboard-card p-3">
            <h5 class="mb-3"><i class="fas fa-tasks me-2"></i>Quick Actions</h5>
            <div class="d-grid gap-2">
                <a href="{{ route('it.news.create') }}" class="btn btn-primary">
                    <i class="fas fa-newspaper me-2"></i> Create News
                </a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-primary">
                    <i class="fas fa-ticket-alt me-2"></i> Ticket Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="d_loader" class="text-center my-4">
                    <div class="spinner-border text-primary"></div>
                </div>
                <div id="d_content" class="d-none">
                    <table class="table table-borderless">
                        <tr><th width="25%">Ticket ID</th><td><span id="d_ticket_id" class="fw-bold text-primary"></span></td></tr>
                        <tr><th>Created by</th><td><span id="d_user"></span></td></tr>
                        <tr><th>Department</th><td><span id="d_department"></span></td></tr>
                        <tr><th>Category</th><td><span id="d_category"></span></td></tr>
                        <tr><th>Status</th><td><span class="badge" id="d_status"></span></td></tr>
                        <tr><th>Priority</th><td><span class="badge" id="d_priority"></span></td></tr>
                        <tr><th>Description</th><td style="white-space: pre-wrap;"><span id="d_description"></span></td></tr>
                        <tr><th>Date</th><td><span id="d_created"></span></td></tr>
                        <tr id="d_row_notes" class="d-none"><th>Resolution Notes</th><td><div id="d_notes" class="text-muted fst-italic bg-light p-2 rounded"></div></td></tr>
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
@endsection