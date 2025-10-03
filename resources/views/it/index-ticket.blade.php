@extends('layouts.it')

@section('title', 'IT Ticket List')
@vite(['resources/css/list-tiket.css','resources/js/it.js'])

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">
            <i class="fas fa-ticket-alt text-primary me-2"></i> IT Ticket List
        </h2>
    </div>

    {{-- Filter Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('it.index-ticket') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search ticket..."
                            value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        @foreach(['waiting','in_progress','pending','resolved','closed'] as $status)
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

    {{-- Ticket Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle text-center">
                <thead class="table-light">
    <tr>
        <th style="width: 8%">ID Ticket</th>
        <th style="width: 20%" class="text-start">Description</th>
        <th style="width: 12%">Department</th> <!-- ✅ Tambahan -->
        <th style="width: 12%">Category</th>
        <th style="width: 12%">Status</th>
        <th style="width: 12%">Priority</th>
        <th style="width: 12%">Created By</th>
        <th style="width: 10%">Action</th>
    </tr>
</thead>
<tbody>
    @forelse($tickets as $ticket)
    <tr data-id="{{ $ticket->id }}">
        <td class="fw-semibold text-primary">#{{ $ticket->ticket_id }}</td>
        <td class="text-start">{{ Str::limit($ticket->description, 60) }}</td>
                <td>{{ $ticket->user->department ?? '-' }}</td> <!-- ✅ Ambil dari users.department -->
        <td><span class="badge rounded-pill bg-info-subtle text-info px-3">{{ $ticket->category->name ?? '-' }}</span></td>
        <td>
            <select name="status" class="form-select form-select-sm update-ticket-field"
                data-id="{{ $ticket->id }}" data-field="status">
                @foreach(['waiting','in_progress','pending','resolved','closed'] as $status)
                <option value="{{ $status }}" @selected($ticket->status == $status)>{{ ucfirst(str_replace('_',' ',$status)) }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <select name="priority" class="form-select form-select-sm update-ticket-field fw-semibold"
                data-id="{{ $ticket->id }}" data-field="priority">
                @foreach(['low','medium','high','urgent'] as $priority)
                <option value="{{ $priority }}" @selected($ticket->priority == $priority)>{{ ucfirst($priority) }}</option>
                @endforeach
            </select>
        </td>
        <td>{{ $ticket->user->name ?? 'Unknown' }}</td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill btn-detail-ticket"
                data-bs-toggle="modal" data-bs-target="#ticketDetailModal"
                data-id="{{ $ticket->ticket_id }}">
                <i class="fas fa-eye me-1"></i> Detail
            </button>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="8" class="text-center text-muted py-4">
            <i class="fas fa-info-circle me-1"></i> No tickets found
        </td>
    </tr>
    @endforelse
</tbody>
            </table>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <span class="text-muted small">Showing {{ $tickets->firstItem() ?? 0 }} - {{ $tickets->lastItem() ?? 0 }} of
                {{ $tickets->total() }}</span>
            <div>{{ $tickets->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
</div>

{{-- Ticket Detail Modal --}}
@include('it.partials.detail-modal')

{{-- Confirm Modal --}}
<div class="modal fade" id="confirmUpdateModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Konfirmasi Perubahan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p id="confirmUpdateMessage"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="confirmUpdateNo" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="confirmUpdateYes">Ya, Ubah</button>
      </div>
    </div>
  </div>
</div>

{{-- Toast --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <i class="fas fa-check-circle me-2 text-success"></i>
      <strong class="me-auto">Notification</strong>
      <small>Now</small>
      <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
    </div>
    <div class="toast-body" id="toast-body-message">Ticket updated successfully.</div>
  </div>
</div>
@endsection
