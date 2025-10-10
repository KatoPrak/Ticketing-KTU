@extends('layouts.staff')

@section('title', 'Staff Dashboard')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')

<div class="welcome-banner">
    <h2>Welcome, <strong>{{ Auth::user()->name }}</strong></h2>
    <p>Department <span class="badge bg-light text-primary">{{ Auth::user()->department }}</span></p>
</div>
{{-- ============================= --}}
{{-- 📰 NEWS SECTION (Modern Quick-Style) --}}
{{-- ============================= --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3">
            <i class="fas fa-bullhorn text-primary me-2"></i> Announcements & News
        </h5>

        @if($news->count() > 0)
            <div class="vstack gap-3">
                @foreach($news as $item)
                <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 hover-shadow-sm" 
                     style="background-color: #fffde7;">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 45px; height: 45px;">
                            <i class="fas fa-info-circle fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-semibold text-dark">{{ $item->message }}</h6>
                            <small class="text-muted">
                                <i class="far fa-clock me-1"></i>
                                {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-2">
                <p class="text-muted mb-0">No news available at the moment.</p>
            </div>
        @endif
    </div>
</div>


{{-- ============================= --}}
{{-- ⚡ QUICK ACTION & TICKETS --}}
{{-- ============================= --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="row">
                    {{-- LEFT COLUMN: Quick Actions --}}
                    <div class="col-lg-4 border-end-lg">
                        <h5 class="mb-3 fw-bold"><i class="fas fa-bolt text-primary me-2"></i>Quick Actions</h5>
                        <div class="d-grid">
                            <button type="button" class="btn btn-primary btn-lg text-start p-3" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                                <div class="d-flex align-items-center">
                                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-plus"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-white">Create New Ticket</h6>
                                        <small class="text-white-50">Report your issue</small>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>

                    {{-- RIGHT COLUMN: Latest Tickets --}}
                    <div class="col-lg-8 ps-lg-4 mt-4 mt-lg-0">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 fw-bold text-dark">
                                <i class="fas fa-history text-muted me-2"></i>Your Latest Tickets
                            </h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead class="text-muted small">
                                    <tr>
                                        <th scope="col" style="size-text: 5px">TICKET ID</th>
                                        <th scope="col">DESCRIPTION</th>
                                        <th scope="col">PRIORITY</th>
                                        <th scope="col" class="text-end">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody id="ticket-list-body" class="align-middle">
                                    {{-- Data will be filled by JavaScript --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================= --}}
{{-- 🎟️ TICKET MODAL --}}
{{-- ============================= --}}
@include('staff.modals.form-ticket')

@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const ticketTableBody = document.getElementById('ticket-list-body');

    // 🎫 FETCH LATEST TICKETS
    function fetchTickets() {
        ticketTableBody.innerHTML = `<tr><td colspan="4" class="text-center p-4">
            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
        </td></tr>`;
        
        fetch(`{{ route('staff.tickets.fetchDashboard') }}`)
            .then(response => response.json())
            .then(data => {
                ticketTableBody.innerHTML = ''; 

                if (data.length > 0) {
                    data.forEach(ticket => {
                        const statusColors = { waiting: 'secondary', open: 'success', progress: 'warning', resolved: 'primary', closed: 'danger' };
                        const priorityColors = { low: 'success', medium: 'warning', high: 'danger' };

                        const row = `
                            <tr>
                                <td class="fw-bold text-primary">${ticket.ticket_id || `#${ticket.id}`}</td>
                                <td>${ticket.description.length > 45 ? ticket.description.substring(0, 45) + '...' : ticket.description}</td>
                                <td><span class="badge bg-${priorityColors[ticket.priority] || 'secondary'}">${ticket.priority}</span></td>
                                <td class="text-end">
                                    <span class="badge rounded-pill bg-${statusColors[ticket.status] || 'secondary'}-subtle text-${statusColors[ticket.status] || 'secondary'} px-3 py-2">
                                        ${ticket.status}
                                    </span>
                                </td>
                            </tr>
                        `;
                        ticketTableBody.insertAdjacentHTML('beforeend', row);
                    });
                } else {
                    ticketTableBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-4">You don't have any tickets yet.</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Error fetching tickets:', error);
                ticketTableBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger py-4">Failed to load data.</td></tr>`;
            });
    }

    fetchTickets();
});
</script>
@endpush