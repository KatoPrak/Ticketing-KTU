@extends('layouts.staff')

@section('title', 'Ticket List')
@section('body-class', 'page-it-tickets')
@vite(['resources/css/list-tiket.css','resources/js/it.js', 'resources/js/ticket-detail-handler.js'])

@section('content')
<div class="container my-2">

    {{-- ================== FILTER ================== --}}
    <div class="filters-section fade-in small-container mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-8">
                <form method="GET" action="{{ route('staff.tickets.index') }}" id="filterForm">
                    <div class="search-box position-relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="search-input form-control pe-5" placeholder="Search tickets..." id="searchInput">
                        <i
                            class="fas fa-search search-icon position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                    </div>
                </form>
            </div>
            <div class="col-12 col-md-4 text-md-end">
                <button type="button" class="btn btn-primary w-100 w-md-auto" data-bs-toggle="modal"
                    data-bs-target="#createTicketModal">
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
                            <th>Department</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="ticketTableBody">
                        @forelse($tickets as $ticket)
                        <tr id="ticket-row-{{ $ticket->id }}">
                            <td>{{ $ticket->ticket_id }}</td>
                            <td>{{ $ticket->user->department->name ?? '-' }}</td>
                            <td>{{ $ticket->category->name ?? '-' }}</td>
                            <td>{{ Str::limit($ticket->description, 50) }}</td>
                            <td>
                                <span class="badge {{ $ticket->status == 'open' ? 'bg-success' : ($ticket->status == 'progress' ? 'bg-warning text-dark' : ($ticket->status == 'resolved' ? 'bg-primary' : ($ticket->status == 'closed' ? 'bg-danger' : 'bg-secondary'))) }}">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $ticket->priority == 'high' ? 'bg-danger' : ($ticket->priority == 'medium' ? 'bg-warning text-dark' : ($ticket->priority == 'low' ? 'bg-success' : 'bg-secondary')) }}">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td>{{ $ticket->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary btn-detail-ticket" data-id="{{ $ticket->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">No active tickets found.</td>
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
                            <th>Department</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="historyTicketsTableBody">
                        @forelse($historyTickets ?? [] as $ticket)
                        @if(is_object($ticket))
                        <tr>
                            <td>{{ $ticket->ticket_id }}</td>
                            <td>{{ $ticket->user->department->name ?? '-' }}</td>
                            <td>{{ $ticket->category->name ?? '-' }}</td>
                            <td>{{ Str::limit($ticket->description, 50) }}</td>
                            <td>
                                <span class="badge {{ $ticket->status == 'resolved' ? 'bg-primary' : ($ticket->status == 'closed' ? 'bg-danger' : 'bg-secondary') }}">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $ticket->priority == 'high' ? 'bg-danger' : ($ticket->priority == 'medium' ? 'bg-warning text-dark' : ($ticket->priority == 'low' ? 'bg-success' : 'bg-secondary')) }}">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td>{{ $ticket->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary btn-detail-ticket" data-id="{{ $ticket->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">No ticket history found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
{{-- 🎟️ CREATE TICKET MODAL --}}
@include('staff.modals.form-ticket')

{{-- ========================================================================= --}}
{{-- ======================== TICKET DETAIL MODAL ============================ --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="detailTicketModal" tabindex="-1" aria-labelledby="detailTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered custom-modal">
        <div class="modal-content shadow-lg border-0">

            {{-- Header --}}
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-ticket-alt me-2"></i> Ticket Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body">

                {{-- Loader --}}
                <div id="d_loader" class="text-center py-4">
                    <div class="spinner-border text-info"></div>
                    <p class="text-muted mt-2">Loading ticket...</p>
                </div>

                {{-- Content --}}
                <div id="d_content" class="d-none">
                    <table class="table table-borderless">
                        <tr>
                            <th>Ticket ID</th>
                            <td id="d_ticket_id">-</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td id="d_description" style="white-space: pre-wrap;">-</td>
                        </tr>
                        <tr>
                            <th>Category</th>
                            <td id="d_category">-</td>
                        </tr>
                        <tr>
                            <th>User</th>
                            <td id="d_user">-</td>
                        </tr>
                        <tr>
                            <th>Created</th>
                            <td id="d_created">-</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span id="d_status" class="badge rounded-pill px-3 py-2 bg-secondary">-</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Priority</th>
                            <td>
                                <span id="d_priority" class="badge rounded-pill px-3 py-2 bg-secondary">-</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Attachments</th>
                            <td id="d_attachments">
                                <span class="text-muted">No attachments</span>
                            </td>
                        </tr>
                        <tr id="d_row_notes" class="d-none">
                            <th>Notes</th>
                            <td id="d_notes">-</td>
                        </tr>
                    </table>
                </div>

            </div>

            {{-- Footer --}}
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>


@endsection
