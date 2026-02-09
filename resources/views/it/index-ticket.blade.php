@extends('layouts.it')

@section('title', 'IT Ticket List')

@section('content')
{{-- FIXED: Hapus container atau ganti dengan container-fluid --}}
@push('styles')
    @vite('resources/css/it-ticket-index.css')
@endpush
<div class="py-0">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-0">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fas fa-ticket-alt text-primary me-2"></i>IT Ticket Dashboard
            </h4>
            <p class="text-muted small mb-0">Manage and track support requests efficiently.</p>
        </div>
        <button class="btn btn-primary px-4 shadow-sm mt-3 mt-md-0 rounded-pill" data-bs-toggle="modal" data-bs-target="#createTicketModal">
            <i class="fas fa-plus me-2"></i>Create Ticket
        </button>
    </div>

    <!-- Filter Section (Simplified) -->
    <div class="bg-white p-3 rounded-4 shadow-sm mb-4">
        <form method="GET" action="{{ route('it.tickets.index') }}" class="row g-3 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search ticket ID, user, or problem..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select text-muted" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    @foreach(['waiting','in_progress','pending','resolved'] as $status)
                    <option value="{{ $status }}" @selected(request('status')==$status)>{{ ucfirst(str_replace('_',' ',$status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select text-muted" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category_id')==$cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 text-end">
                <a href="{{ route('it.tickets.index') }}" class="btn btn-light text-muted w-100" title="Reset Filter"><i class="fas fa-redo"></i></a>
            </div>
        </form>
    </div>

    <!-- Ticket Table Section -->
    <div class="bg-white rounded-4 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary small text-uppercase">
                    <tr>
                        <th class="py-3 ps-4 border-0 rounded-start-4">Ticket</th>
                        <th class="py-3 border-0">Requester</th>
                        <th class="py-3 border-0">Issue</th>
                        <th class="py-3 border-0">Status</th>
                        <th class="py-3 border-0">Priority</th>
                        <th class="py-3 border-0">Date</th>
                        <th class="py-3 pe-4 text-end border-0 rounded-end-4">Action</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($tickets as $ticket)
                    @php
                        $priorityColors = [
                            'low' => '#6c757d',      // Secondary
                            'medium' => '#0d6efd',   // Primary
                            'high' => '#ffc107',     // Warning
                            'urgent' => '#fd7e14',   // Orange
                            'critical' => '#dc3545'  // Danger
                        ];
                        $rowBorderColor = $priorityColors[$ticket->priority] ?? '#6c757d';
                    @endphp
                    <tr class="ticket-row transition-hover" style="border-left: 5px solid {{ $rowBorderColor }};">
                        <td class="ps-3">
                            <span class="text-dark fw-bold">#{{ $ticket->ticket_id }}</span>
                            @if($ticket->transferLogs->isNotEmpty())
                                <div class="mt-1"><span class="badge bg-warning text-dark" style="font-size: 0.65rem;">Transferred</span></div>
                            @endif
                        </td>
                        <td>
                            <div>
                                <h6 class="mb-0 small fw-semibold text-dark">{{ $ticket->user->name ?? 'Unknown' }}</h6>
                                <span class="text-muted extra-small" style="font-size: 11px;">
                                    {{ optional($ticket->user->department)->name ?? '-' }} | {{ optional($ticket->user->location)->name ?? '-' }}
                                </span>
                            </div>
                        </td>
                        <td style="max-width: 250px;">
                            <span class="d-block text-truncate text-secondary" style="font-size: 0.95rem;">
                                {{ $ticket->description }}
                            </span>
                            <small class="text-muted">{{ $ticket->category->name ?? '-' }}</small>
                        </td>
                        <td>
                            @php
                                $statusClasses = [
                                    'waiting' => 'text-secondary bg-light',
                                    'in_progress' => 'text-warning-emphasis bg-warning-subtle',
                                    'pending' => 'text-info-emphasis bg-info-subtle',
                                    'resolved' => 'text-success bg-success-subtle',
                                    'closed' => 'text-danger bg-danger-subtle'
                                ];
                                $currStatusClass = $statusClasses[$ticket->status] ?? 'text-secondary bg-light';
                            @endphp
                            <select class="form-select form-select-sm border-0 shadow-none fw-bold {{ $currStatusClass }} update-ticket-field px-3" 
                                    style="width: auto; cursor: pointer; border-radius: 20px;" 
                                    data-id="{{ $ticket->id }}" data-field="status" data-original-value="{{ $ticket->status }}">
                                @foreach(['waiting','in_progress','pending','resolved','closed'] as $s)
                                <option value="{{ $s }}" @selected($ticket->status == $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-select form-select-sm border-0 shadow-none select-priority-{{$ticket->priority}} update-ticket-field" 
                                    style="width: 100px; cursor: pointer;"
                                    data-id="{{ $ticket->id }}" data-field="priority" data-original-value="{{ $ticket->priority }}">
                                @foreach(['low','medium','high','urgent','critical'] as $priority)
                                <option value="{{ $priority }}" @selected($ticket->priority == $priority)>{{ ucfirst($priority) }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-muted small">
                            {{ $ticket->created_at->format('d M, H:i') }}
                        </td>
                        <td class="pe-4 text-end">
                            <button class="btn btn-icon btn-light text-warning rounded-circle btn-transfer-ticket me-1" 
                                    data-id="{{ $ticket->id }}" 
                                    data-region-id="{{ $ticket->region_id }}"
                                    title="Transfer Ticket">
                                <i class="fas fa-exchange-alt"></i>
                            </button>
                            <button class="btn btn-icon btn-light text-primary rounded-circle btn-detail-ticket" data-id="{{ $ticket->id }}" title="View Details">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">No active tickets found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <!-- Pagination -->
        @if($tickets->hasPages())
        <div class="px-4 py-3 border-top d-flex flex-column flex-md-row justify-content-between align-items-center bg-white">
            <div class="text-muted small mb-2 mb-md-0">
                Showing <span class="fw-bold text-dark">{{ $tickets->firstItem() }}</span> to <span class="fw-bold text-dark">{{ $tickets->lastItem() }}</span> of <span class="fw-bold text-dark">{{ $tickets->total() }}</span> tickets
            </div>
            <div class="custom-pagination">
                {{ $tickets->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>

<style>
/* Custom Tweaks for "Simpler Modern" look */
.table-hover tbody tr:hover {
    background-color: #f8fafc;
}
.transition-hover {
    transition: background-color 0.2s ease;
}
.btn-icon {
    width: 36px; height: 36px;
    display: inline-flex; align-items: center; justify-content: center;
    transition: all 0.2s;
}
.btn-icon:hover {
    background-color: #e0e7ff; /* indigo-100 */
    transform: translateX(3px);
}
.extra-small { font-size: 0.75rem; }
.rounded-4 { border-radius: 1rem !important; }
.form-select:focus { box-shadow: none; border-color: #cbd5e1; }
.avatar-initial { font-family: 'Poppins', sans-serif; }

/* Elegant Pagination */
.custom-pagination .pagination {
    margin-bottom: 0;
    gap: 5px;
}
.custom-pagination .page-item .page-link {
    border: none;
    border-radius: 8px; /* Soft square */
    color: #64748b; /* slate-500 */
    font-weight: 500;
    padding: 0.5rem 0.85rem;
    transition: all 0.2s ease;
    background-color: transparent;
}
.custom-pagination .page-item .page-link:hover {
    background-color: #f1f5f9; /* slate-100 */
    color: #0f172a; /* slate-900 */
    transform: translateY(-1px);
}
.custom-pagination .page-item.active .page-link {
    background-color: #0d6efd; /* Primary */
    color: #fff;
    box-shadow: 0 2px 4px rgba(13, 110, 253, 0.3);
}
.custom-pagination .page-item.disabled .page-link {
    color: #cbd5e1;
    background-color: transparent;
}
.custom-pagination .page-item:first-child .page-link,
.custom-pagination .page-item:last-child .page-link {
    border-radius: 8px; /* Maintain shape for next/prev */
    font-size: 0.9rem;
}

</style>

@include('it.partials.ticket-detail-modal')

{{-- ================= RESOLUTION NOTES MODAL ================= --}}
<div class="modal fade" id="resolutionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add Resolution</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Please provide notes for changing the status to Pending/Resolved/Closed.</p>
                <textarea id="resolutionNotes" class="form-control" rows="4"
                    placeholder="Write ticket completion notes..."></textarea>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="saveResolutionBtn" class="btn btn-primary">Save Update</button>
            </div>
        </div>
    </div>
</div>

{{-- ================= CREATE TICKET MODAL ================= --}}
<div class="modal fade" id="createTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Create Ticket for User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="createTicketForm" enctype="multipart/form-data">
                <div class="modal-body p-4">

                    <div class="row g-3">
                        {{-- Select User --}}
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-uppercase text-muted">Select User</label>
                            <select id="it_create_ticket_user" name="user_id" class="form-select" required>
                                <option value="" selected disabled>-- Choose User --</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" 
                                            data-location="{{ optional($u->location)->name ?? '-' }}"
                                            data-department="{{ optional($u->department)->name ?? '-' }}">
                                        {{ $u->name }} ({{ $u->id_staff }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- User Information Details (Read-only) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">User Location</label>
                            <input type="text" id="user_display_location" class="form-control bg-light" value="-" readonly disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">User Department</label>
                            <input type="text" id="user_display_department" class="form-control bg-light" value="-" readonly disabled>
                        </div>
                        {{-- Category --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="" selected disabled>-- Choose Category --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Priority --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Priority</label>
                            <select name="priority" class="form-select" required>
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase text-muted">Issue Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Describe the problem..." required></textarea>
                        </div>

                        {{-- Attachments --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase text-muted">Attachments (Optional)</label>
                            <input type="file" name="attachments[]" class="form-control" multiple accept="image/*">
                            <div class="form-text small">Max 5MB per file. Formats: JPG, PNG, HEIC.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="fas fa-save me-1"></i> Create Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================= TRANSFER TICKET MODAL ================= --}}
<div class="modal fade" id="transferTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
             <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-exchange-alt me-2"></i>Transfer Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="transferTicketForm">
                    <input type="hidden" id="transfer_ticket_id" name="ticket_id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Select Target Region</label>
                        <select id="transfer_region" name="region_id" class="form-select" required>
                             <option value="" selected disabled>-- Choose Region --</option>
                             @foreach(\App\Models\Region::all() as $region)
                                <option value="{{ $region->id }}">{{ $region->name }}</option>
                             @endforeach
                        </select>
                        <div class="form-text small">Ticket will be auto-assigned to IT Staff in this region.</div>
                    </div>

                     <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Note (Optional)</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Reason for transfer..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                 <button type="button" id="submitTransferBtn" class="btn btn-warning fw-bold">Transfer Ticket</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // EXISTING: Detail Ticket Logic is in it-ticket-index.js/admin-....js
    // We add Create Ticket Logic here
    
    const createTicketForm = document.getElementById('createTicketForm');
    const userSelect = document.getElementById('it_create_ticket_user');
    const locationInput = document.getElementById('user_display_location');
    const departmentInput = document.getElementById('user_display_department');

    if (userSelect) {
        userSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption) {
                locationInput.value = selectedOption.dataset.location || '-';
                departmentInput.value = selectedOption.dataset.department || '-';
            }
        });
    }

    if (createTicketForm) {
        createTicketForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Creating...';
            btn.disabled = true;

            const formData = new FormData(this);

            fetch('{{ route("it.tickets.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message (using SweetAlert if available or Alert)
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Ticket created successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                             window.location.reload();
                        });
                    } else {
                        alert('Ticket created successfully!');
                        window.location.reload();
                    }
                } else {
                    const msg = data.message || 'Unknown error';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', msg, 'error');
                    } else {
                        alert('Error: ' + msg);
                    }
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'An error occurred. Please try again.', 'error');
                } else {
                    alert('An error occurred. Please try again.');
                }
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
    }

    // --------------------------------------------------------------------------
    // TRANSFER TICKET LOGIC
    // --------------------------------------------------------------------------
    const transferModal = new bootstrap.Modal(document.getElementById('transferTicketModal'));
    let transferTicketId = null;

    document.querySelectorAll('.btn-transfer-ticket').forEach(btn => {
        btn.addEventListener('click', function() {
            transferTicketId = this.dataset.id;
            // Get current region from data-region-id attribute on the button
            const currentRegionId = this.dataset.regionId;
            
            document.getElementById('transfer_ticket_id').value = transferTicketId;
            
            // Filter options
            const select = document.getElementById('transfer_region');
            Array.from(select.options).forEach(option => {
                if(option.value == currentRegionId) {
                    option.style.display = 'none';
                    if(option.selected) select.value = "";
                } else {
                    option.style.display = '';
                }
            });

            transferModal.show();
        });
    });

    /* REGION BASED TRANSFER - NO AJAX NEEDED FOR STAFF SELECT */
    /*
    document.getElementById('transfer_location').addEventListener('change', function() {
        // Removed as per request to use Regional only
    });
    */

    document.getElementById('submitTransferBtn').addEventListener('click', function() {
        const form = document.getElementById('transferTicketForm');
        if(!form.checkValidity()) { form.reportValidity(); return; }

        const formData = new FormData(form);
        const btn = this;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Transferring...';

        const payload = Object.fromEntries(formData);

        fetch(`/it/tickets/${transferTicketId}/transfer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
             if(data.success) {
                 if(typeof Swal !== 'undefined') {
                    Swal.fire('Success', data.message, 'success').then(() => window.location.reload());
                 } else {
                    alert(data.message);
                    window.location.reload();
                 }
                 transferModal.hide();
             } else {
                 throw new Error(data.message || 'Transfer failed');
             }
        })
        .catch(err => {
            if(typeof Swal !== 'undefined') Swal.fire('Error', err.message, 'error');
            else alert(err.message);
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });

});
</script>
@endpush