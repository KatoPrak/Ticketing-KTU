@extends('layouts.it')

@section('title', 'Dashboard - IT Team')

@section('content')

@push('styles')
    @vite('resources/css/dashboard-it.css')
    <style>
        /* Responsive Table Action (Mobile Sticky Columns) */
        @media (max-width: 767.98px) {
            /* Sticky Status Column (4th column) - Simple style */
            .table-responsive th:nth-child(4),
            .table-responsive td:nth-child(4) {
                position: sticky !important;
                right: 100px !important; /* Width of Action column for dashboard */
                background-color: #fff !important;
                z-index: 6 !important;
            }
            
            /* Status header */
            .table-responsive thead th:nth-child(4) {
                background-color: #f8f9fa !important;
                z-index: 16 !important;
            }
            
            /* Hover state for status column */
            .table-hover tbody tr:hover td:nth-child(4) {
                background-color: inherit !important;
            }
            
            /* Sticky Action Column */
            .table-responsive th:last-child, 
            .table-responsive td:last-child {
                position: sticky;
                right: 0;
                background-color: #fff;
                z-index: 5;
                box-shadow: -2px 0 5px rgba(0,0,0,0.1);
                border-left: 1px solid #f0f0f0;
                text-align: center !important;
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
            .table-hover tbody tr:hover td:last-child {
                background-color: #f8f9fa;
            }
            /* Header slightly different bg */
            .table-responsive th:last-child {
                background-color: #f8f9fa;
            }
        }
    </style>
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
                    </span>
                    <span class="user-name-highlight text-break mt-1 mt-md-0">{{ Auth::user()->name }}!</span>
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
                            <th>Location</th>
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
                                <td>
                                    <span class="badge bg-light text-dark border" title="{{ $ticket->user->location->name ?? 'Unknown' }}">
                                        <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                                        {{ Str::limit($ticket->user->location->name ?? 'Unknown', 20) }}
                                    </span>
                                    @if($ticket->assigned_to == Auth::id())
                                        <div class="mt-1"><span class="badge bg-warning text-dark" style="font-size: 0.7rem;">Transferred</span></div>
                                    @endif
                                </td>
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

@include('it.partials.ticket-detail-modal')
@endsection