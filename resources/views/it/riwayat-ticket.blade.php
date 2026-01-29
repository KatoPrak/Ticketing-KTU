@extends('layouts.it')

@section('title', 'IT Ticket History')

@section('content')
<div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title">
            <i class="fas fa-history me-2 text-primary"></i> IT Ticket History
        </h2>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form id="filterForm" method="GET" action="{{ route('it.tickets.history') }}">
                <div class="row g-3">
                    <!-- Search Input -->
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="Search by description, ticket ID, or name..." 
                                   value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Date Range -->
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="{{ request('start_date') }}">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="{{ request('end_date') }}">
                    </div>
                    
                    <!-- Category Filter -->
                    <div class="col-md-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Priority Filter -->
                    <div class="col-md-3">
                        <label for="priority" class="form-label">Priority</label>
                        <select class="form-select" id="priority" name="priority">
                            <option value="">All Priorities</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="critical" {{ request('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="d-grid gap-2 w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i> Apply Filters
                            </button>
                            <a href="{{ route('it.tickets.history') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Active Filters -->
    @if(request()->hasAny(['search', 'start_date', 'end_date', 'category', 'priority']))
    <div class="mb-3">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <span class="text-muted">Active filters:</span>
            @if(request('search'))
                <span class="badge bg-light text-dark">
                    Search: "{{ request('search') }}" 
                    <a href="{{ remove_filter_url('search') }}" class="remove-filter ms-1">×</a>
                </span>
            @endif
            @if(request('start_date'))
                <span class="badge bg-light text-dark">
                    From: {{ request('start_date') }}
                    <a href="{{ remove_filter_url('start_date') }}" class="remove-filter ms-1">×</a>
                </span>
            @endif
            @if(request('end_date'))
                <span class="badge bg-light text-dark">
                    To: {{ request('end_date') }}
                    <a href="{{ remove_filter_url('end_date') }}" class="remove-filter ms-1">×</a>
                </span>
            @endif
            @if(request('category'))
                @php
                    $categoryName = $categories->where('id', request('category'))->first()->name ?? 'Unknown';
                @endphp
                <span class="badge bg-light text-dark">
                    Category: {{ $categoryName }}
                    <a href="{{ remove_filter_url('category') }}" class="remove-filter ms-1">×</a>
                </span>
            @endif
            @if(request('priority'))
                <span class="badge bg-light text-dark">
                    Priority: {{ ucfirst(request('priority')) }}
                    <a href="{{ remove_filter_url('priority') }}" class="remove-filter ms-1">×</a>
                </span>
            @endif
        </div>
    </div>
    @endif

    <!-- Table Tickets -->
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <!-- Results Summary -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="mb-0 text-muted">
                    Showing {{ $tickets->firstItem() ?? 0 }} - {{ $tickets->lastItem() ?? 0 }} of {{ $tickets->total() }} tickets
                </p>
            </div>
            
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Ticket ID</th>
                        <th>Name</th>
                        <th>Problem</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Department</th>
                        <th>Resolved/Closed</th>
                    </tr>
                </thead>
                <tbody id="riwayatTbody">
                @forelse($tickets as $ticket)
                    <tr>
                        <td>#{{ $ticket->ticket_id }}</td>
                        <td>{{ $ticket->user->name ?? 'Unknown' }}</td>
                        <td>{{ Str::limit($ticket->description, 50) }}</td>
                        <td>{{ $ticket->category->name ?? '-' }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ ucfirst($ticket->status) }}</span>
                        </td>
                        <td>
                            @php
                                $priorityColors = [
                                    'low' => 'bg-info',
                                    'medium' => 'bg-primary',
                                    'high' => 'bg-warning',
                                    'urgent' => 'bg-danger',
                                    'critical' => 'bg-dark'
                                ];
                                $priorityColor = $priorityColors[$ticket->priority] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $priorityColor }}">{{ ucfirst($ticket->priority) }}</span>
                        </td>
                        <td>{{ $ticket->department->name ?? '-' }}</td>
                        <td>{{ $ticket->updated_at->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-3 d-block"></i>
                            @if(request()->hasAny(['search', 'start_date', 'end_date', 'category', 'priority']))
                                No tickets found matching your criteria
                            @else
                                No closed tickets available
                            @endif
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $tickets->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Clear search input
        document.getElementById('clearSearch').addEventListener('click', function() {
            document.getElementById('search').value = '';
            document.getElementById('filterForm').submit();
        });
        
        // Set max date for end_date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('end_date').max = today;
        
        // Validate date range
        document.getElementById('start_date').addEventListener('change', function() {
            const endDate = document.getElementById('end_date');
            if (this.value && endDate.value && this.value > endDate.value) {
                endDate.value = this.value;
            }
        });
        
        document.getElementById('end_date').addEventListener('change', function() {
            const startDate = document.getElementById('start_date');
            if (this.value && startDate.value && this.value < startDate.value) {
                startDate.value = this.value;
            }
        });
    });
</script>
@endpush

@php
function remove_filter_url($filterName) {
    $currentUrl = request()->fullUrl();
    $url = preg_replace('/([?&])'.$filterName.'=[^&]+(&|$)/', '$1', $currentUrl);
    $url = rtrim($url, '?&');
    return $url;
}
@endphp

<style>
    .remove-filter {
        text-decoration: none;
        color: #6c757d;
        font-weight: bold;
    }
    .remove-filter:hover {
        color: #dc3545;
    }
</style>
@endsection