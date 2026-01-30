@extends('layouts.it')

@section('title', 'Dashboard - IT Team')

@section('content')

@push('styles')
    @vite('resources/css/dashboard-it.css')
@endpush

@push('scripts')
    @vite('resources/js/dashboard-it.js')
@endpush

<div class="welcome-card mb-4">
    <div class="row align-items-center">
        <div class="col-md-8 col-12">
            <div class="greeting-container">
                <h2 class="animated-greeting">
                    <i class="fas fa-hand-sparkles me-2 wave-animation"></i>
                    <span class="greeting-text">Hi IT Team! All is Well!</span>
                    <span class="user-name-highlight">{{ Auth::user()->name }}</span>!
                </h2>
                <p class="mb-2 animated-fade-in">
                    <i class="fas fa-building me-2"></i>
                    {{ optional(Auth::user()->department)->name ?? '-' }} 
                    <span class="badge bg-primary ms-2">{{ Auth::user()->id_staff }}</span>
                </p>
                <div class="datetime-display animated-fade-in-delayed">
                    <i class="fas fa-calendar-day me-2 text-primary"></i>
                    <span id="currentDate" class="fw-semibold"></span>
                    <span class="mx-2">•</span>
                    <i class="fas fa-clock me-2 text-primary pulse-animation"></i>
                    <span id="currentTime" class="fw-semibold"></span>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-12 text-center text-md-end mt-3 mt-md-0">
            <div class="user-icon-wrapper">
                <i class="fas fa-user-tie user-icon"></i>
            </div>
        </div>
    </div>
</div>





{{-- Dashboard Stats --}}
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

{{-- Recent Activity --}}
<div class="row">
    <div class="col-lg-8 col-12">
        <div class="dashboard-card p-3">
            <h5 class="mb-3"><i class="fas fa-chart-line me-2"></i>Recent Activity</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>Name</th>
                            <th>Issue/Problem</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Report Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTickets as $ticket)
                            <tr>
                                <td><strong>{{ $ticket->ticket_id ?? ('#'.$ticket->id) }}</strong></td>
                                <td>
                                    @if($ticket->user)
                                        <span>{{ $ticket->user->name }}</span>
                                    @elseif($ticket->customer)
                                        <span>{{ $ticket->customer->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
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
                                <td colspan="7" class="text-center text-muted py-4">
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

{{-- ✅ MODAL DETAIL WITH TIMELINE --}}
<div class="modal fade" id="detailTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 900px;">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-ticket-alt me-2"></i> Ticket Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Loader --}}
                <div id="d_loader" class="text-center my-4">
                    <div class="spinner-border text-info"></div>
                    <p class="text-muted mt-2">Loading ticket...</p>
                </div>

                {{-- Content with Timeline --}}
                <div id="d_content" class="d-none">
                    <div class="row">
                        {{-- LEFT COLUMN: Ticket Info --}}
                        <div class="col-md-7">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th width="35%" class="text-muted">Ticket ID</th>
                                    <td>
                                        <span id="d_ticket_id" class="fw-bold text-primary">-</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Name</th>
                                    <td id="d_user">-</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Department</th>
                                    <td id="d_department">-</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Category</th>
                                    <td id="d_category">-</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Status</th>
                                    <td>
                                        <span id="d_status" class="badge rounded-pill px-3 py-2 bg-secondary">-</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Priority</th>
                                    <td>
                                        <span id="d_priority" class="badge rounded-pill px-3 py-2 bg-secondary">-</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted align-top">Issue/Problem</th>
                                    <td id="d_description" style="white-space: pre-wrap;">-</td>
                                </tr>
                                <tr id="d_row_notes" class="d-none">
                                    <th class="text-muted align-top">Solution</th>
                                    <td>
                                        <div id="d_notes" class="text-muted fst-italic bg-light p-2 rounded">-</div>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted align-top">Attachments</th>
                                    <td id="d_attachments">
                                            <span class="text-muted">
                                                <i class="fas fa-paperclip me-1"></i>No attachments
                                            </span>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        {{-- RIGHT COLUMN: Timeline --}}
                        <div class="col-md-5">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3">
                                        <i class="fas fa-clock me-2 text-info"></i>Timeline
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
                <i class="fas fa-hourglass-start me-1"></i>Waiting for response
            </div>
        </div>
    </div>

    {{-- RESOLVED/CLOSED --}}
    <div class="timeline-item">
        <div class="timeline-marker bg-success" id="d_resolved_marker">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="timeline-content">
            <div class="timeline-title text-success fw-bold" id="d_resolved_title">
                <i class="fas fa-tasks me-1"></i>Resolved/Closed
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
@endsection