@extends('layouts.it')

@section('title', 'Ticket Queue')


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
    <div class="bg-white rounded-4 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary small text-uppercase">
                    <tr>
                        <th class="py-3 ps-4 border-0 rounded-start-4">Ticket ID</th>
                        <th class="py-3 border-0">Requester</th>
                        <th class="py-3 border-0">Problem</th>
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
                        <td class="ps-3" data-label="Ticket">
                            <span class="text-dark fw-bold">{{ $ticket->ticket_id }}</span>
                            @if($ticket->transferLogs->isNotEmpty())
                                <div class="mt-1"><span class="badge bg-warning text-dark" style="font-size: 0.65rem;">Transferred</span></div>
                            @endif
                            @if(!$ticket->assigned_to)
                                <div class="mt-1"><span class="badge bg-danger" style="font-size: 0.65rem;">UNASSIGNED</span></div>
                            @endif
                        </td>
                        <td data-label="Requester">
                            <div>
                                <h6 class="mb-0 small fw-semibold text-dark">{{ $ticket->user->name ?? 'Unknown' }}</h6>
                                <span class="text-muted extra-small" style="font-size: 11px;">
                                    {{ optional($ticket->user->department)->name ?? '-' }} | {{ optional($ticket->user->location)->name ?? '-' }}
                                </span>
                            </div>
                        </td>
                        <td style="max-width: 250px;" data-label="Issue">
                            <span class="d-block text-truncate text-secondary" style="font-size: 0.95rem;">
                                {{ $ticket->description }}
                            </span>
                            <small class="text-muted">{{ $ticket->category->name ?? '-' }}</small>
                        </td>
                        <td data-label="Status">
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
                                    style="width: 100%; cursor: pointer; border-radius: 20px;" 
                                    data-id="{{ $ticket->id }}" data-field="status" data-original-value="{{ $ticket->status }}">
                                @foreach(['waiting','in_progress','pending','resolved','closed'] as $s)
                                <option value="{{ $s }}" @selected($ticket->status == $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td data-label="Priority">
                            <select class="form-select form-select-sm border-0 shadow-none select-priority-{{$ticket->priority}} update-ticket-field" 
                                    style="width: 100px; cursor: pointer;"
                                    data-id="{{ $ticket->id }}" data-field="priority" data-original-value="{{ $ticket->priority }}">
                                @foreach(['low','medium','high','urgent','critical'] as $priority)
                                <option value="{{ $priority }}" @selected($ticket->priority == $priority)>{{ ucfirst($priority) }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-muted small" data-label="Date">
                            {{ $ticket->created_at->format('d M, H:i') }}
                        </td>
                        <td class="pe-4 text-end" data-label="Action">
                            <button class="btn btn-icon btn-light text-warning rounded-circle btn-transfer-ticket me-1" 
                                    data-id="{{ $ticket->id }}" 
                                    data-region-id="{{ $ticket->region_id }}"
                                    title="Transfer Ticket">
                                <i class="fas fa-exchange-alt"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary btn-detail-ticket" data-id="{{ $ticket->id }}" title="View Details">
                                <i class="fas fa-eye"></i>
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

/* Mobile Responsive Enhancements */
@media (max-width: 767.98px) {
    /* Ensure table-responsive allows sticky positioning */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        position: relative;
        max-width: 100vw;
    }
    
    /* Force table to allow horizontal scroll */
    .table-responsive table {
        min-width: 100%;
        width: max-content;
    }
    
    /* Sticky Table Header - Always visible while scrolling */
    .table-responsive thead th {
        position: sticky;
        top: 0;
        background-color: #f8f9fa !important;
        z-index: 10;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    /* ========================================
       MOBILE CARD LAYOUT - Show All Columns
       ======================================== */
    
    /* Hide table header on mobile */
    .table-responsive thead {
        display: none;
    }
    
    /* Transform table to card layout */
    .table-responsive,
    .table-responsive table,
    .table-responsive tbody {
        display: block;
        width: 100%;
    }
    
    .table-responsive tr {
        display: block;
        margin-bottom: 1rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 1rem;
        border-left: 5px solid #0d6efd; /* Keep priority color indicator */
    }
    
    .table-responsive td {
        display: block;
        width: 100% !important;
        padding: 0.5rem 0 !important;
        border: none !important;
        text-align: left !important;
        position: relative !important;
        background: transparent !important;
        box-shadow: none !important;
    }
    
    /* Add labels before each field - STRENGTHENED */
    .table-responsive td:before {
        content: attr(data-label) !important;
        font-weight: 700 !important;
        font-size: 0.75rem !important;
        color: #6c757d !important;
        text-transform: uppercase !important;
        display: block !important;
        margin-bottom: 0.25rem !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    /* Ticket ID - First item, larger */
    .table-responsive td:nth-child(1) {
        font-size: 1.1rem;
        font-weight: 700;
        color: #0d6efd;
        padding-bottom: 0.75rem !important;
        border-bottom: 1px solid #e9ecef;
        margin-bottom: 0.5rem;
    }
    
    /* Requester info */
    .table-responsive td:nth-child(2) {
        font-size: 0.9rem;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    /* Issue/Problem - FORCE DISPLAY */
    .table-responsive td:nth-child(3) {
        font-size: 0.85rem !important;
        color: #495057 !important;
        padding-bottom: 0.75rem !important;
        border-bottom: 1px solid #e9ecef !important;
        margin-bottom: 0.5rem !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        max-width: 100% !important;
    }
    
    .table-responsive td:nth-child(3) * {
        display: block !important;
        visibility: visible !important;
    }
    
    /* Status - FORCE DISPLAY */
    .table-responsive td:nth-child(4) {
        padding: 0.75rem 0 !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        margin-bottom: 0.5rem !important;
    }
    
    .table-responsive td:nth-child(4) * {
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    .table-responsive td:nth-child(4) .form-select {
        font-size: 0.85rem !important;
        padding: 0.5rem !important;
        font-weight: 600 !important;
        width: 100% !important;
    }
    
    /* Priority */
    .table-responsive td:nth-child(5) {
        padding: 0.75rem 0 !important;
        display: block !important;
        visibility: visible !important;
    }
    
    .table-responsive td:nth-child(5) .form-select {
        font-size: 0.85rem !important;
        padding: 0.5rem !important;
        width: 100% !important;
    }
    
    /* Date */
    .table-responsive td:nth-child(6) {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    /* Action buttons */
    .table-responsive td:nth-child(7) {
        padding-top: 1rem !important;
        border-top: 1px solid #e9ecef;
        margin-top: 0.5rem;
        display: flex !important;
        gap: 0.5rem;
        justify-content: flex-start;
    }
    
    .table-responsive td:nth-child(7):before {
        display: none; /* Hide "Action" label */
    }
    
    .table-responsive td:nth-child(7) .btn {
        flex: 1;
        margin: 0 !important;
    }
    
    /* Remove sticky positioning on mobile */
    .table-responsive th,
    .table-responsive td {
        position: static !important;
        right: auto !important;
    }
}

    
    /* Action header needs higher z-index */
    .table-responsive thead th:last-child {
        background-color: #f8f9fa !important;
        z-index: 15 !important;
    }
    
    /* Hover state */
    .table-hover tbody tr:hover td:last-child {
        background-color: #f8fafc !important;
    }
    
    /* Ensure buttons are visible and properly sized */
    .table-responsive td:last-child .btn {
        margin: 0 2px !important;
        display: inline-block !important;
        visibility: visible !important;
    }
    
    /* Make sure action column header text is visible */
    .table-responsive th:last-child {
        font-size: 0.75rem !important;
    }
}



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
                        <div class="form-text small">Ticket will be sent to the unassigned queue of the target region.</div>
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
            
            const inputTicketId = document.getElementById('transfer_ticket_id');
            if (inputTicketId) inputTicketId.value = transferTicketId;
            
            // Filter options
            const select = document.getElementById('transfer_region');
            if(select) {
                select.value = ""; // Reset logic
                Array.from(select.options).forEach(option => {
                    if(option.value == currentRegionId) {
                        option.style.display = 'none';
                    } else {
                        option.style.display = '';
                    }
                });
            }

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

    // --------------------------------------------------------------------------
    // FIX ARIA-HIDDEN ACCESSIBILITY WARNINGS
    // --------------------------------------------------------------------------
    // Remove focus from modal elements before they are hidden
    document.getElementById('transferTicketModal')?.addEventListener('hide.bs.modal', function() {
        if (document.activeElement && this.contains(document.activeElement)) {
            document.activeElement.blur();
        }
    });
    
    document.getElementById('detailTicketModal')?.addEventListener('hide.bs.modal', function() {
        if (document.activeElement && this.contains(document.activeElement)) {
            document.activeElement.blur();
        }
    });

});
</script>
@endpush