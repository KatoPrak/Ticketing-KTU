@extends('layouts.it')

@section('title', 'IT Dashboard')


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
        <div class="dashboard-card p-2 shadow-sm" style="min-height: 100%;">
            <div class="d-flex justify-content-between align-items-center mb-2 px-2 pt-1">
                <h6 class="m-0 fw-bold text-secondary" style="font-size: 0.85rem;">
                    <i class="fas fa-history me-1"></i> Recent Activity
                </h6>
                <a href="{{ route('it.tickets.index') }}" class="text-decoration-none" style="font-size: 0.75rem;">View All</a>
            </div>
            <div class="table-responsive mobile-card-table">
                <table class="table table-hover table-sm align-middle mb-0 text-nowrap" style="font-size: 0.75rem;">
                    <thead class="bg-light table-light">
                        <tr>
                            <th class="ps-3 border-bottom-0">ID</th>
                            <th class="border-bottom-0">Loc</th>
                            <th class="border-bottom-0">User</th>
                            <th class="border-bottom-0">Issue</th>
                            <th class="border-bottom-0">Sts</th>
                            <th class="border-bottom-0">Prio</th>
                            <th class="border-bottom-0">Date</th>
                            <th class="text-end pe-3 border-bottom-0">Act</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($recentTickets as $ticket)
                            <tr>
                                <td class="ps-3">
                                    <span class="fw-bold text-primary">{{ $ticket->ticket_id ?? ('#'.$ticket->id) }}</span>
                                    @if($ticket->transferLogs->isNotEmpty())
                                        <i class="fas fa-exchange-alt text-warning ms-1" title="Transferred" style="font-size: 0.7rem;"></i>
                                    @endif
                                    @if(!$ticket->assigned_to)
                                        <span class="badge bg-danger ms-1" style="font-size: 0.6rem; vertical-align: middle;">NEW</span>
                                    @endif
                                </td>
                                <td>
                                    @if($ticket->user && $ticket->user->location)
                                        <span class="text-truncate d-inline-block" style="max-width: 60px;" title="{{ $ticket->user->location->name }}">
                                            {{ \Illuminate\Support\Str::limit($ticket->user->location->name, 10) }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 80px;" title="{{ $ticket->user->name ?? ($ticket->customer->name ?? '-') }}">
                                        {{ \Illuminate\Support\Str::limit($ticket->user->name ?? ($ticket->customer->name ?? '-'), 12) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 100px;" title="{{ $ticket->description }}">
                                        {{ \Illuminate\Support\Str::limit($ticket->description, 20) }}
                                    </span>
                                </td>
                                <td>
                                    {{-- Compact Badge --}}
                                    @php
                                        $statusClass = match($ticket->status) {
                                            'open' => 'bg-primary',
                                            'resolved' => 'bg-success',
                                            'closed' => 'bg-secondary',
                                            'pending' => 'bg-warning text-dark',
                                            'waiting' => 'bg-info text-dark',
                                            default => 'bg-light text-dark border'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }} rounded-pill" style="font-size: 0.65rem; padding: 0.2rem 0.5rem;">
                                        {{ ucfirst(str_replace('_',' ', $ticket->status)) }}
                                    </span>
                                </td>
                                <td>
                                     <span class="badge bg-light text-dark border rounded-pill" style="font-size: 0.65rem; padding: 0.2rem 0.5rem;">{{ ucfirst($ticket->priority) }}</span>
                                </td>
                                <td class="text-muted">{{ optional($ticket->created_at)->format('d/m H:i') }}</td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-light btn-sm p-1 border rounded-circle btn-detail-ticket"
                                        data-id="{{ $ticket->id }}" title="View Details" style="width: 24px; height: 24px; line-height: 1;">
                                        <i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">
                                    <small>No recent activity</small>
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