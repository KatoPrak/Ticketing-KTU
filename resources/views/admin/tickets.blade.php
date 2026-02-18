@extends('layouts.admin')

@section('title', 'All Tickets')


@push('styles')

@endpush
<style>
    .ticket-container {
        padding: 15px;
        max-width: 100%;
    }

    .ticket-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
    }

    /* Header Section */
    .card-header-custom {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        padding: 14px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .card-header-custom h3 {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Filter Section */
    .filter-section {
        background: #f8fafc;
        padding: 14px 18px;
        border-bottom: 1px solid #e2e8f0;
    }

    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: flex-end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
        min-width: 140px;
    }

    .filter-group label {
        font-size: 11px;
        font-weight: 600;
        color: #475569;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .filter-group select {
        padding: 7px 10px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 12px;
        background: white;
        transition: all 0.2s;
        cursor: pointer;
    }

    .filter-group select:focus {
        border-color: #4f46e5;
        outline: none;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .filter-group select:hover {
        border-color: #cbd5e1;
    }

    .filter-buttons {
        display: flex;
        gap: 8px;
        align-items: flex-end;
    }

    /* Buttons */
    .btn-custom {
        padding: 7px 14px;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }

    .btn-filter {
        background: #4f46e5;
        color: white;
    }

    .btn-filter:hover {
        background: #4338ca;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.3);
    }

    .btn-reset {
        background: #64748b;
        color: white;
    }

    .btn-reset:hover {
        background: #475569;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(100, 116, 139, 0.3);
    }

    .btn-export {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        color: white;
    }

    .btn-export:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
    }

    /* Table Container */
    .table-container {
        overflow-x: auto;
        padding: 0;
    }

    .table-custom {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 11px;
        min-width: 1200px;
    }

    .table-custom thead {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table-custom thead th {
        background: linear-gradient(180deg, #1e293b 0%, #334155 100%);
        color: white;
        font-weight: 700;
        padding: 10px 8px;
        text-align: left;
        border-bottom: 2px solid #4f46e5;
        white-space: nowrap;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .table-custom tbody td {
        padding: 8px;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
        background: white;
    }

    .table-custom tbody tr {
        transition: all 0.15s;
    }

    .table-custom tbody tr:hover {
        background: #f8fafc;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }

    /* Column Specific Styles */
    .ticket-id {
        font-weight: 700;
        color: #4f46e5;
        white-space: nowrap;
        font-family: 'Courier New', monospace;
        font-size: 11px;
    }

    .ticket-desc {
        max-width: 180px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: #334155;
        line-height: 1.4;
    }

    /* Date Time Cell */
    .datetime-cell {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .date-part {
        font-weight: 600;
        color: #1e293b;
        font-size: 11px;
    }

    .time-part {
        font-size: 9px;
        color: #64748b;
        font-family: 'Courier New', monospace;
    }

    .no-date {
        color: #94a3b8;
        font-style: italic;
        font-size: 10px;
        text-align: center;
    }

    /* Badges */
    .badge-custom {
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
        display: inline-block;
    }

    /* Priority Badges */
    .badge-critical {
        background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%);
        color: white;
    }

    .badge-urgent,
    .badge-high {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        color: white;
    }

    .badge-medium {
        background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        color: white;
    }

    .badge-low {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
    }

    /* Status Badges */
    .badge-open {
        background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
        color: white;
    }

    .badge-in-progress {
        background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        color: white;
    }

    .badge-resolved {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
    }

    .badge-pending {
        background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
        color: white;
    }

    .badge-waiting {
        background: linear-gradient(135deg, #06b6d4 0%, #22d3ee 100%);
        color: white;
    }

    .badge-closed {
        background: linear-gradient(135deg, #64748b 0%, #94a3b8 100%);
        color: white;
    }

    /* Rating Stars */
    .rating-stars {
        color: #fbbf24;
        font-size: 11px;
        white-space: nowrap;
    }

    .rating-stars i {
        margin-right: 1px;
    }

    .rating-stars .text-muted {
        color: #cbd5e1 !important;
    }

    .no-feedback-badge {
        background: #f1f5f9;
        color: #64748b;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 9px;
        font-weight: 600;
    }

    /* Comment Cell */
    .comment-cell {
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: help;
        color: #475569;
    }

    .marking-cell {
        max-width: 140px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: help;
        color: #475569;
    }

    .text-muted-custom {
        color: #94a3b8;
        font-style: italic;
    }

    /* No Data */
    .no-data {
        padding: 40px 20px;
        text-align: center;
        color: #94a3b8;
    }

    .no-data i {
        font-size: 48px;
        color: #e2e8f0;
        margin-bottom: 15px;
    }

    .no-data p {
        font-size: 14px;
        margin: 0;
    }

    /* Pagination */
    .pagination-container {
        padding: 14px 18px;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .pagination-info {
        font-size: 11px;
        color: #64748b;
        font-weight: 500;
    }

    .pagination-container nav {
        margin: 0;
    }

    /* Bootstrap Pagination Customization */
    .pagination {
        margin: 0;
        gap: 4px;
    }

    .pagination .page-link {
        font-size: 11px;
        padding: 5px 10px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        color: #4f46e5;
        font-weight: 600;
        transition: all 0.2s;
    }

    .pagination .page-link:hover {
        background: #4f46e5;
        color: white;
        border-color: #4f46e5;
    }

    .pagination .page-item.active .page-link {
        background: #4f46e5;
        border-color: #4f46e5;
        color: white;
    }

    .pagination .page-item.disabled .page-link {
        background: #f1f5f9;
        border-color: #e2e8f0;
        color: #cbd5e1;
    }
    /* Enhanced Pagination Styles */
.pagination-container {
    padding: 14px 18px;
    border-top: 1px solid #e2e8f0;
    background: #f8fafc;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}

.pagination-info {
    font-size: 11px;
    color: #64748b;
    font-weight: 500;
}

.pagination-info strong {
    color: #4f46e5;
    font-weight: 700;
}

.pagination-wrapper {
    display: flex;
    align-items: center;
}

/* Page Jumper */
.page-jumper {
    display: flex;
    align-items: center;
    gap: 8px;
}

.page-jumper form {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
}

.page-jumper label {
    font-size: 11px;
    color: #64748b;
    font-weight: 600;
    margin: 0;
    white-space: nowrap;
}

.page-jump-input {
    width: 60px;
    padding: 5px 8px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 11px;
    text-align: center;
    transition: all 0.2s;
}

.page-jump-input:focus {
    border-color: #4f46e5;
    outline: none;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.btn-jump {
    padding: 5px 10px;
    background: #4f46e5;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 11px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-jump:hover {
    background: #4338ca;
    transform: translateY(-1px);
}

/* Bootstrap Pagination Customization */
.pagination {
    margin: 0;
    gap: 4px;
}

.pagination .page-item {
    margin: 0;
}

.pagination .page-link {
    font-size: 11px;
    padding: 5px 10px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    color: #4f46e5;
    font-weight: 600;
    transition: all 0.2s;
    margin: 0 2px;
}

.pagination .page-link:hover {
    background: #4f46e5;
    color: white;
    border-color: #4f46e5;
    transform: translateY(-1px);
}

.pagination .page-item.active .page-link {
    background: #4f46e5;
    border-color: #4f46e5;
    color: white;
    box-shadow: 0 2px 6px rgba(79, 70, 229, 0.3);
}

.pagination .page-item.disabled .page-link {
    background: #f1f5f9;
    border-color: #e2e8f0;
    color: #cbd5e1;
    cursor: not-allowed;
}

/* First/Last page buttons */
.pagination .page-item:first-child .page-link,
.pagination .page-item:last-child .page-link {
    font-weight: 700;
}

/* Responsive Design */
@media (max-width: 768px) {
    .pagination-container {
        padding: 12px 14px;
        flex-direction: column;
        text-align: center;
    }

    .pagination-info {
        order: 1;
        width: 100%;
        text-align: center;
    }

    .pagination-wrapper {
        order: 2;
        width: 100%;
        justify-content: center;
        margin: 8px 0;
    }

    .page-jumper {
        order: 3;
        width: 100%;
        justify-content: center;
    }

    .pagination {
        justify-content: center;
        flex-wrap: wrap;
    }

    .pagination .page-link {
        padding: 4px 8px;
        font-size: 10px;
    }
}

@media (max-width: 480px) {
    .page-jumper label {
        display: none;
    }

    .page-jump-input {
        width: 50px;
        font-size: 10px;
    }

    .pagination .page-link {
        padding: 3px 6px;
        font-size: 9px;
        margin: 0 1px;
    }
}

/* Loading state for pagination */
.pagination-loading {
    opacity: 0.5;
    pointer-events: none;
}

/* Smooth scroll to top when changing pages */
@media (prefers-reduced-motion: no-preference) {
    html {
        scroll-behavior: smooth;
    }
}

    /* Responsive Design */
    @media (max-width: 1200px) {
        .filter-form {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-group {
            width: 100%;
        }

        .filter-buttons {
            width: 100%;
        }

        .btn-custom {
            flex: 1;
            justify-content: center;
        }
    }

    @media (max-width: 768px) {
        .ticket-container {
            padding: 10px;
        }

        .card-header-custom {
            padding: 12px 14px;
            flex-direction: column;
            align-items: stretch;
        }

        .card-header-custom h3 {
            font-size: 15px;
            justify-content: center;
        }

        .btn-export {
            width: 100%;
            justify-content: center;
        }

        .filter-section {
            padding: 12px 14px;
        }

        .table-container {
            margin: 0 -10px;
            border-radius: 0;
        }

        .table-custom {
            font-size: 10px;
            min-width: 1100px;
        }

        .table-custom thead th {
            padding: 8px 6px;
            font-size: 9px;
        }

        .table-custom tbody td {
            padding: 6px;
        }

        .ticket-desc,
        .marking-cell,
        .comment-cell {
            max-width: 120px;
        }

        .pagination-container {
            padding: 12px 14px;
            flex-direction: column;
            text-align: center;
        }

        .pagination-info {
            order: 2;
            margin-top: 8px;
        }

        .pagination {
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .card-header-custom h3 {
            font-size: 14px;
        }

        .btn-custom {
            font-size: 11px;
            padding: 6px 12px;
        }

        .filter-group select {
            font-size: 11px;
            padding: 6px 8px;
        }
    }

    /* Scrollbar Styling */
    .table-container::-webkit-scrollbar {
        height: 6px;
    }

    .table-container::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .table-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@section('content')
<div class="ticket-container">
    <div class="ticket-card">
        <!-- Header -->
        <div class="card-header-custom">
            <h3>
                <i class="fas fa-ticket-alt"></i> Ticket Management
            </h3>
            <a href="{{ route('admin.tickets.export.pdf', request()->query()) }}" class="btn-custom btn-export" target="_blank">
                <i class="fas fa-file-pdf"></i> Export to PDF
            </a>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <form action="{{ route('admin.tickets.index') }}" method="GET" class="filter-form">
                <div class="filter-group">
                    <label for="year">
                        <i class="fas fa-calendar"></i> Year
                    </label>
                    <select name="year" id="year">
                        <option value="">All Years</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="month">
                        <i class="fas fa-calendar-alt"></i> Month
                    </label>
                    <select name="month" id="month">
                        <option value="">All Months</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="filter-group">
                    <label for="status">
                        <i class="fas fa-tasks"></i> Status
                    </label>
                    <select name="status" id="status">
                        <option value="">All Status</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="waiting" {{ request('status') == 'waiting' ? 'selected' : '' }}>Waiting</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="per_page">
                        <i class="fas fa-list"></i> Per Page
                    </label>
                    <select name="per_page" id="per_page">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>

                <div class="filter-buttons">
                    <button type="submit" class="btn-custom btn-filter">
                        <i class="fas fa-filter"></i> Apply Filter
                    </button>
                    <a href="{{ route('admin.tickets.index') }}" class="btn-custom btn-reset">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="table-container">
            <table class="table-custom" id="ticketsTable">
                <thead>
                    <tr>
                        <th>Ticket ID</th>
                        <th>User</th>
                        <th>Location</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Report Date</th>
                        <th>Response Date</th>
                        <th>Resolved/Closed</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Marking</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr>
                            <td>
                                <span class="ticket-id">{{ $ticket->ticket_id }}</span>
                            </td>
                            <td>{{ $ticket->user->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-light text-dark border" title="{{ $ticket->user->location->name ?? 'Unknown' }}">
                                    <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                                    {{ Str::limit($ticket->user->location->name ?? 'Unknown', 15) }}
                                </span>
                            </td>
                            <td>{{ $ticket->category->name ?? 'N/A' }}</td>
                            <td>
                                <div class="ticket-desc" title="{{ $ticket->description }}">
                                    {{ $ticket->description }}
                                </div>
                            </td>
                            <td>
                                <span class="badge-custom
                                    @if($ticket->priority === 'critical') badge-critical
                                    @elseif($ticket->priority === 'urgent' || $ticket->priority === 'high') badge-high
                                    @elseif($ticket->priority === 'medium') badge-medium
                                    @else badge-low @endif">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-custom
                                    @if($ticket->status === 'in_progress') badge-in-progress
                                    @elseif($ticket->status === 'resolved') badge-resolved
                                    @elseif($ticket->status === 'pending') badge-pending
                                    @elseif($ticket->status === 'waiting') badge-waiting
                                    @elseif($ticket->status === 'closed') badge-closed
                                    @else badge-open @endif">
                                    {{ str_replace('_', ' ', ucfirst($ticket->status)) }}
                                </span>
                            </td>
                            <td>
                                <div class="datetime-cell">
                                    <span class="date-part">{{ $ticket->created_at->format('d M Y') }}</span>
                                    <span class="time-part">{{ $ticket->created_at->format('H:i:s') }}</span>
                                </div>
                            </td>
                            {{-- ✅ RESPONSE DATE = updated_at --}}
                            <td>
                                @if($ticket->updated_at)
                                    <div class="datetime-cell">
                                        <span class="date-part">{{ $ticket->updated_at->format('d M Y') }}</span>
                                        <span class="time-part">{{ $ticket->updated_at->format('H:i:s') }}</span>
                                    </div>
                                @else
                                    <span class="no-date">Not yet</span>
                                @endif
                            </td>
                            {{-- ✅ RESOLVED/CLOSED DATE = resolved_at --}}
                            <td>
                                @if($ticket->resolved_at)
                                    <div class="datetime-cell">
                                        <span class="date-part">{{ \Carbon\Carbon::parse($ticket->resolved_at)->format('d M Y') }}</span>
                                        <span class="time-part">{{ \Carbon\Carbon::parse($ticket->resolved_at)->format('H:i:s') }}</span>
                                    </div>
                                @else
                                    <span class="no-date">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($ticket->feedback)
                                    <div class="rating-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $ticket->feedback->rating)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star text-muted"></i>
                                            @endif
                                        @endfor
                                    </div>
                                @else
                                    <span class="no-feedback-badge">No Rating</span>
                                @endif
                            </td>
                            <td>
                                @if($ticket->feedback && $ticket->feedback->comment)
                                    <div class="comment-cell" title="{{ $ticket->feedback->comment }}">
                                        {{ $ticket->feedback->comment }}
                                    </div>
                                @else
                                    <span class="text-muted-custom">-</span>
                                @endif
                            </td>
                            <td>
                                @if($ticket->resolution_notes)
                                    <div class="marking-cell" title="{{ $ticket->resolution_notes }}">
                                        {{ $ticket->resolution_notes }}
                                    </div>
                                @else
                                    <span class="text-muted-custom">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13">
                                <div class="no-data">
                                    <i class="fas fa-inbox"></i>
                                    <p>No tickets found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Enhanced Pagination -->
@if($tickets->hasPages())
<div class="pagination-container">
    <div class="pagination-info">
        <span>
            Showing 
            <strong>{{ $tickets->firstItem() ?? 0 }}</strong> 
            to 
            <strong>{{ $tickets->lastItem() ?? 0 }}</strong> 
            of 
            <strong>{{ $tickets->total() }}</strong> 
            entries
        </span>
    </div>
    
    <div class="pagination-wrapper">
        {{ $tickets->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
    
    {{-- Optional: Quick page jumper --}}
    <div class="page-jumper">
        <form action="{{ route('admin.tickets.index') }}" method="GET" id="pageJumpForm">
            @foreach(request()->except('page') as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            
            <label for="jump_to_page">Go to page:</label>
            <input 
                type="number" 
                name="page" 
                id="jump_to_page" 
                min="1" 
                max="{{ $tickets->lastPage() }}"
                placeholder="{{ $tickets->currentPage() }}"
                class="page-jump-input"
            >
            <button type="submit" class="btn-jump">
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>
    </div>
</div>
@else
<div class="pagination-container">
    <div class="pagination-info">
        <span>Showing <strong>{{ $tickets->count() }}</strong> entries</span>
    </div>
</div>
@endif
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('build/assets/admin-CBTWxBS5.js') }}"></script>
<script>
    // Optional: Auto-scroll to top when pagination changes
document.addEventListener('DOMContentLoaded', function() {
    const pageLinks = document.querySelectorAll('.pagination .page-link');
    
    pageLinks.forEach(link => {
        link.addEventListener('click', function() {
            // Scroll to top of table
            document.querySelector('.ticket-card').scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        });
    });

    // Page jumper form validation
    const pageJumpForm = document.getElementById('pageJumpForm');
    if (pageJumpForm) {
        pageJumpForm.addEventListener('submit', function(e) {
            const input = document.getElementById('jump_to_page');
            const value = parseInt(input.value);
            const max = parseInt(input.max);
            
            if (value < 1 || value > max || isNaN(value)) {
                e.preventDefault();
                alert(`Please enter a page number between 1 and ${max}`);
                input.focus();
            }
        });
    }
});
</script>
@endpush