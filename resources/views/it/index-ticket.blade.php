@extends('layouts.it')

@section('title', 'IT Ticket List')
@vite(['resources/css/it-tickets.css', 'resources/js/it-tickets.js']) {{-- Disarankan ganti nama agar lebih spesifik --}}

@section('content')
<div class="container py-4">

    <h2 class="fw-bold mb-3"><i class="fas fa-ticket-alt text-primary me-2"></i> IT Ticket List</h2>

    {{-- ================= FILTER ================= --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('it.index-ticket') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by ID or description..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach(['waiting','in_progress','pending','resolved'] as $status)
                            <option value="{{ $status }}" @selected(request('status') == $status)>
                                {{ ucfirst(str_replace('_',' ',$status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>
                                {{ $cat->name }}
                            </option>
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
                    {{-- ✅ PERUBAHAN ADA DI BARIS INI --}}
                    <tr id="ticket-row-{{ $ticket->id }}" class="priority-{{ $ticket->priority }}">
                        <td data-label="Ticket ID">#{{ $ticket->ticket_id }}</td>
                        <td data-label="Description" class="text-start">{{ Str::limit($ticket->description, 60) }}</td>
                        <td data-label="Department">{{ $ticket->user->department ?? '-' }}</td>
                        <td data-label="Category">{{ $ticket->category->name ?? '-' }}</td>
                        <td data-label="Status">
                            <select class="form-select form-select-sm update-ticket-field select-status-{{$ticket->status}}"
                                    data-id="{{ $ticket->id }}" data-field="status" data-original-value="{{ $ticket->status }}">
                                @foreach(['waiting','in_progress','pending','resolved','closed'] as $status)
                                    <option value="{{ $status }}" @selected($ticket->status == $status)>
                                        {{ ucfirst(str_replace('_',' ',$status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td data-label="Priority">
                            <select class="form-select form-select-sm update-ticket-field select-priority-{{$ticket->priority}}"
                                    data-id="{{ $ticket->id }}" data-field="priority" data-original-value="{{ $ticket->priority }}">
                                @foreach(['low','medium','high','urgent'] as $priority)
                                    <option value="{{ $priority }}" @selected($ticket->priority == $priority)>
                                        {{ ucfirst($priority) }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td data-label="Created By">{{ $ticket->user->name ?? '-' }}</td>
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
        <div id="d_loader" class="text-center my-4"><div class="spinner-border text-primary"></div></div>
        <div id="d_content" class="d-none">
          <table class="align-middle mb-0">
            <tr><th width="25%">Ticket ID</th><td><span id="d_ticket_id" class="fw-bold text-primary"></span></td></tr>
            <tr><th>Created By</th><td><span id="d_user"></span></td></tr>
            <tr><th>Department</th><td><span id="d_department"></span></td></tr>
            <tr><th>Category</th><td><span id="d_category"></span></td></tr>
            <tr><th>Status</th><td><span class="badge" id="d_status"></span></td></tr>
            <tr><th>Priority</th><td><span class="badge" id="d_priority"></span></td></tr>
            <tr><th>Description</th><td style="white-space: pre-wrap;"><span id="d_description"></span></td></tr>
            <tr><th>Date</th><td><span id="d_created"></span></td></tr>
            <tr id="d_row_notes" class="d-none"><th class="align-text-top">Resolution Notes</th><td><div id="d_notes" class="text-muted fst-italic bg-light p-2 rounded"></div></td></tr>
            <tr><th class="align-text-top">Attachments</th><td><div id="d_attachments" class="d-flex flex-wrap gap-2"></div></td></tr>
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
  <div class="modal-dialog">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Resolution Notes</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <textarea id="resolutionNotes" class="form-control" rows="4" placeholder="Write ticket completion notes..."></textarea>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="saveResolutionBtn" class="btn btn-primary">Save Notes</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {

    document.body.addEventListener('change', async (e) => {
        if (!e.target.classList.contains('update-ticket-field')) return;

        const select = e.target;
        const id = select.dataset.id;
        const field = select.dataset.field;
        const value = select.value;
        const old = select.dataset.originalValue;

        if (!confirm(`Ubah ${field} menjadi "${value}"?`)) {
            select.value = old;
            return;
        }

        // jika status pending/closed → buka modal catatan
        if (field === 'status' && ['pending', 'closed'].includes(value)) {
            const modal = new bootstrap.Modal(document.getElementById('resolutionModal'));
            document.getElementById('saveResolutionBtn').onclick = async () => {
                const notes = document.getElementById('resolutionNotes').value.trim();
                await updateFieldWithNotes(id, field, value, old, notes);
                modal.hide();
            };
            modal.show();
        } else {
            await updateFieldWithNotes(id, field, value, old, null);
        }
    });

    async function updateFieldWithNotes(id, field, value, old, notes) {
        try {
            const res = await fetch(`/it/tickets/${id}/update-field`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ field, value, resolution_notes: notes })
            });

            const data = await res.json();
            if (data.success) {
                alert('Berhasil memperbarui tiket.');
            } else {
                alert(data.message);
                document.querySelector(`[data-id="${id}"][data-field="${field}"]`).value = old;
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan server.');
            document.querySelector(`[data-id="${id}"][data-field="${field}"]`).value = old;
        }
    }

});
</script>
@endpush
