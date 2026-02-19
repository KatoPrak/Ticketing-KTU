@extends('layouts.staff')

@section('title', 'My Tickets')

@section('body-class', 'page-it-tickets')
<link rel="stylesheet" href="{{ asset('build/assets/list-tiket-C-bDVq2X.css') }}">

{{-- ✅ TAMBAHKAN CSRF TOKEN --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

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
        <i class="fas fa-list"></i> Active Tickets
    </div>
    <div id="ticketsContent">
        <div class="table-responsive" id="ticketsTableWrapper">
            <table class="table table-hover align-middle" id="ticketsTable">
                <thead class="table-light">
                    <tr>
                        <th>Ticket ID</th>
                        <th>Problem</th>
                        <th>Department</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Reported Date</th>
                        <th>Response Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="ticketTableBody">
                    @forelse($tickets as $ticket)
                    <tr id="ticket-row-{{ $ticket->id }}">
                        <td>{{ $ticket->ticket_id }}</td>
                        <td>{{ Str::limit($ticket->description, 50) }}</td>
                        <td>{{ $ticket->user->department->name ?? '-' }}</td>
                        <td>{{ $ticket->category->name ?? '-' }}</td>
                        <td>
                            @php
                                $statusClass = match($ticket->status) {
                                    'open' => 'bg-success',
                                    'waiting' => 'bg-info',
                                    'in_progress' => 'bg-warning text-dark',
                                    'pending' => 'bg-warning text-dark',
                                    'resolved' => 'bg-primary',
                                    'closed' => 'bg-secondary',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </td>
                        <td>
                            @php
                                $priorityClass = match($ticket->priority) {
                                    'critical' => 'bg-dark',
                                    'urgent' => 'bg-danger',
                                    'high' => 'bg-danger',
                                    'medium' => 'bg-warning text-dark',
                                    'low' => 'bg-success',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $priorityClass }}">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">
                                {{ $ticket->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                            </small>
                        </td>
                        <td>
                            <small class="text-muted">
                                @if($ticket->updated_at)
                                   {{ $ticket->updated_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                                @else
                                    Not Yet Responded
                                @endif
                            </small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-primary btn-detail-ticket" data-id="{{ $ticket->id }}" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-delete-ticket" data-id="{{ $ticket->id }}" data-ticket-id="{{ $ticket->ticket_id }}" title="Delete Ticket">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="fas fa-inbox text-muted" style="font-size: 2rem;"></i>
                            <p class="mt-2 text-muted">No active tickets found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination untuk Active Tickets --}}
        <div class="d-flex justify-content-end mt-3 pagination-wrapper">
            {{ $tickets->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- ================== TICKET HISTORY ================== --}}
<div class="tickets-container fade-in mb-5">
    <div class="tickets-header mb-3">
        <i class="fas fa-archive"></i> Ticket History (Closed & Resolved)
    </div>
    <div id="historyTicketsContent">
        <div class="table-responsive" id="historyTicketsTableWrapper">
            <table class="table table-hover align-middle" id="historyTicketsTable">
                <thead class="table-light">
                    <tr>
                        <th>Ticket ID</th>
                        <th>Problem</th>
                        <th>Department</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Reported Date</th>
                        <th>Solved Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="historyTicketsTableBody">
                    @forelse($historyTickets as $ticket)
                    <tr id="ticket-row-{{ $ticket->id }}">
                        <td>{{ $ticket->ticket_id }}</td>
                        <td>{{ Str::limit($ticket->description, 50) }}</td>
                        <td>{{ $ticket->user->department->name ?? '-' }}</td>
                        <td>{{ $ticket->category->name ?? '-' }}</td>
                        <td>
                            @php
                                $statusClass = match($ticket->status) {
                                    'resolved' => 'bg-primary',
                                    'closed' => 'bg-secondary',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </td>
                        <td>
                            @php
                                $priorityClass = match($ticket->priority) {
                                    'critical' => 'bg-dark',
                                    'urgent' => 'bg-danger',
                                    'high' => 'bg-danger',
                                    'medium' => 'bg-warning text-dark',
                                    'low' => 'bg-success',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $priorityClass }}">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">
                                {{ $ticket->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                            </small>
                        </td>
                        <td>
                            <small class="text-muted">
                                @if($ticket->resolved_at)
                                    {{ $ticket->resolved_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                                @else
                                    {{ $ticket->updated_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                                @endif
                            </small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-primary btn-detail-ticket" data-id="{{ $ticket->id }}" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if($ticket->feedback)
                                    <button class="btn btn-sm btn-success btn-view-feedback" data-id="{{ $ticket->id }}" title="View Feedback">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-warning btn-add-feedback" data-id="{{ $ticket->id }}" data-ticket-id="{{ $ticket->ticket_id }}" title="Add Feedback">
                                        <i class="fas fa-star"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="fas fa-folder-open text-muted" style="font-size: 2rem;"></i>
                            <p class="mt-2 text-muted">No ticket history found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination untuk History Tickets --}}
        <div class="d-flex justify-content-end mt-3 pagination-wrapper">
            {{ $historyTickets->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- 🎟️ MODALS --}}
@include('staff.modals.form-ticket')        {{-- Create Ticket Modal --}}
@include('staff.modals.show-ticket-modal')  {{-- Detail Ticket Modal --}}

{{-- ⭐ FEEDBACK MODAL --}}
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="feedbackModalLabel">
                    <i class="fas fa-star me-2"></i>Ticket Feedback
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="feedbackForm">
                    <input type="hidden" id="feedback_ticket_id" name="ticket_id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ticket ID</label>
                        <p class="form-control-plaintext" id="feedback_ticket_number"></p>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Rating <span class="text-danger">*</span></label>
                        <div class="rating-container text-center py-3">
                            <div class="star-rating" id="starRating">
                                <i class="fas fa-star star" data-rating="1"></i>
                                <i class="fas fa-star star" data-rating="2"></i>
                                <i class="fas fa-star star" data-rating="3"></i>
                                <i class="fas fa-star star" data-rating="4"></i>
                                <i class="fas fa-star star" data-rating="5"></i>
                            </div>
                            <input type="hidden" id="rating" name="rating" required>
                            <p class="mt-2 mb-0 text-muted" id="ratingText">Select a rating</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="feedback_comment" class="form-label fw-bold">Comment <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="feedback_comment" name="comment" rows="4" 
                            placeholder="Share your experience with our IT support service..." required></textarea>
                        <div class="form-text">Please provide your feedback about the ticket resolution.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" id="submitFeedback">
                    <i class="fas fa-paper-plane me-1"></i>Submit Feedback
                </button>
            </div>
        </div>
    </div>
</div>

{{-- 👁️ VIEW FEEDBACK MODAL --}}
<div class="modal fade" id="viewFeedbackModal" tabindex="-1" aria-labelledby="viewFeedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="viewFeedbackModalLabel">
                    <i class="fas fa-check-circle me-2"></i>Ticket Feedback
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Ticket ID</label>
                    <p class="form-control-plaintext" id="view_feedback_ticket_number"></p>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Rating</label>
                    <div class="text-center py-2">
                        <div id="viewStarRating" class="fs-4"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Comment</label>
                    <div class="card bg-light">
                        <div class="card-body">
                            <p class="mb-0" id="view_feedback_comment"></p>
                        </div>
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label fw-bold">Submitted</label>
                    <p class="form-control-plaintext text-muted" id="view_feedback_date"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.star-rating {
    display: inline-flex;
    gap: 0.5rem;
}

.star-rating .star {
    font-size: 2.5rem;
    color: #ddd;
    cursor: pointer;
    transition: all 0.2s ease;
}

.star-rating .star:hover,
.star-rating .star.active {
    color: #ffc107;
    transform: scale(1.1);
}

.star-rating .star.active {
    animation: starPulse 0.3s ease;
}

@keyframes starPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.2); }
}

.rating-container {
    background: #f8f9fa;
    border-radius: 10px;
}

</style>

@endsection

{{-- ✅ SUCCESS TOAST NOTIFICATION --}}
@if(session('success'))
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
    <div id="ticketSuccessToast" class="toast align-items-center text-white bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                🎟️ {{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎯 Ticket List Page - Handler Loaded');
    
    // Debug: Check if elements exist
    const createForm = document.getElementById('createTicketForm');
    const createModal = document.getElementById('createTicketModal');
    
    console.log('🔍 Debug Ticket List Page:');
    console.log('createTicketForm:', createForm);
    console.log('createTicketModal:', createModal);
    console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.content);
    


    // ⭐ ADD FEEDBACK HANDLER
    document.addEventListener('click', async function(e) {
        // ADD FEEDBACK BUTTON
        if (e.target.closest('.btn-add-feedback')) {
            const button = e.target.closest('.btn-add-feedback');
            const ticketId = button.getAttribute('data-id');
            const ticketNumber = button.getAttribute('data-ticket-id');
            
            console.log('⭐ Add feedback for ticket:', ticketId);
            
            // Reset form
            document.getElementById('feedbackForm').reset();
            document.getElementById('feedback_ticket_id').value = ticketId;
            document.getElementById('feedback_ticket_number').textContent = ticketNumber;
            document.getElementById('rating').value = '';
            document.getElementById('ratingText').textContent = 'Select a rating';
            
            // Reset stars
            document.querySelectorAll('.star-rating .star').forEach(star => {
                star.classList.remove('active');
                star.style.color = '#ddd';
            });
            
            // Show modal
            const feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal'));
            feedbackModal.show();
            return;
        }
        
        // VIEW FEEDBACK BUTTON
        if (e.target.closest('.btn-view-feedback')) {
            const button = e.target.closest('.btn-view-feedback');
            const ticketId = button.getAttribute('data-id');
            
            console.log('👁️ View feedback for ticket:', ticketId);
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                
                const response = await fetch(`/staff/tickets/${ticketId}/feedback`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                if (data.success && data.feedback) {
                    const feedback = data.feedback;
                    
                    // Populate modal
                    document.getElementById('view_feedback_ticket_number').textContent = data.ticket_id;
                    document.getElementById('view_feedback_comment').textContent = feedback.comment;
                    document.getElementById('view_feedback_date').textContent = feedback.resolved_at;
                    
                    // Display stars
                    const rating = feedback.rating;
                    let starsHtml = '';
                    for (let i = 1; i <= 5; i++) {
                        if (i <= rating) {
                            starsHtml += '<i class="fas fa-star text-warning"></i> ';
                        } else {
                            starsHtml += '<i class="far fa-star text-muted"></i> ';
                        }
                    }
                    document.getElementById('viewStarRating').innerHTML = starsHtml;
                    
                    // Show modal
                    const viewFeedbackModal = new bootstrap.Modal(document.getElementById('viewFeedbackModal'));
                    viewFeedbackModal.show();
                } else {
                    throw new Error('Feedback not found');
                }
            } catch (error) {
                console.error('❌ View feedback error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to load feedback',
                    icon: 'error',
                    confirmButtonColor: '#d33'
                });
            }
            return;
        }

        // DELETE TICKET BUTTON
        if (e.target.closest('.btn-delete-ticket')) {
            const button = e.target.closest('.btn-delete-ticket');
            const ticketId = button.getAttribute('data-id');
            const ticketNumber = button.getAttribute('data-ticket-id');
            
            console.log('🗑️ Delete button clicked for ticket:', ticketId);
            
            // Show confirmation dialog
            const result = await Swal.fire({
                title: 'Delete Ticket?',
                html: `Are you sure you want to delete ticket <strong>${ticketNumber}</strong>?<br><small class="text-muted">This action cannot be undone.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash me-1"></i> Yes, Delete',
                cancelButtonText: '<i class="fas fa-times me-1"></i> Cancel',
                reverseButtons: true
            });
            
            if (result.isConfirmed) {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                    
                    // Show loading state
                    Swal.fire({
                        title: 'Deleting...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    const response = await fetch(`/staff/tickets/${ticketId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.message || `Server error: ${response.status}`);
                    }
                    
                    if (data.success) {
                        // Remove the row from table with animation
                        const row = document.getElementById(`ticket-row-${ticketId}`);
                        if (row) {
                            row.style.transition = 'opacity 0.3s';
                            row.style.opacity = '0';
                            setTimeout(() => row.remove(), 300);
                        }
                        
                        // Show success message
                        Swal.fire({
                            title: 'Deleted!',
                            text: data.message || 'Ticket has been deleted successfully.',
                            icon: 'success',
                            confirmButtonColor: '#3085d6',
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => {
                            // Reload to update the list
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.message || 'Failed to delete ticket');
                    }
                    
                } catch (error) {
                    console.error('❌ Delete ticket error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: error.message,
                        icon: 'error',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Close'
                    });
                }
            }
        }
        

        
        // TICKET DETAIL HANDLER is managed by ticket-detail-handler.js (loaded globally)
    });


    // ⭐ STAR RATING HANDLER
    const stars = document.querySelectorAll('.star-rating .star');
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            document.getElementById('rating').value = rating;
            
            // Update stars visual
            stars.forEach(s => {
                const starRating = s.getAttribute('data-rating');
                if (starRating <= rating) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
            
            // Update text
            const ratingTexts = {
                '1': 'Poor',
                '2': 'Fair',
                '3': 'Good',
                '4': 'Very Good',
                '5': 'Excellent'
            };
            document.getElementById('ratingText').textContent = ratingTexts[rating];
        });
        
        // Hover effect
        star.addEventListener('mouseenter', function() {
            const rating = this.getAttribute('data-rating');
            stars.forEach(s => {
                const starRating = s.getAttribute('data-rating');
                if (starRating <= rating) {
                    s.style.color = '#ffc107';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });
    });
    
    // Reset stars on mouse leave
    document.getElementById('starRating').addEventListener('mouseleave', function() {
        const currentRating = document.getElementById('rating').value;
        stars.forEach(s => {
            const starRating = s.getAttribute('data-rating');
            if (currentRating && starRating <= currentRating) {
                s.style.color = '#ffc107';
            } else {
                s.style.color = '#ddd';
            }
        });
    });

    // ✅ SUBMIT FEEDBACK HANDLER
    document.getElementById('submitFeedback')?.addEventListener('click', async function() {
        const form = document.getElementById('feedbackForm');
        const ticketId = document.getElementById('feedback_ticket_id').value;
        const rating = document.getElementById('rating').value;
        const comment = document.getElementById('feedback_comment').value;
        
        // Validation
        if (!rating) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please select a rating',
                icon: 'warning',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        if (!comment.trim()) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please provide a comment',
                icon: 'warning',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Submitting...';
            
            const response = await fetch(`/staff/tickets/${ticketId}/feedback`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    rating: rating,
                    comment: comment
                })
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || `Server error: ${response.status}`);
            }
            
            if (data.success) {
                // Close modal
                const feedbackModal = bootstrap.Modal.getInstance(document.getElementById('feedbackModal'));
                feedbackModal.hide();
                
                // Show success message
                Swal.fire({
                    title: 'Success!',
                    text: 'Thank you for your feedback!',
                    icon: 'success',
                    confirmButtonColor: '#3085d6',
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error(data.message || 'Failed to submit feedback');
            }
            
        } catch (error) {
            console.error('❌ Submit feedback error:', error);
            Swal.fire({
                title: 'Error!',
                text: error.message,
                icon: 'error',
                confirmButtonColor: '#d33'
            });
        } finally {
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Submit Feedback';
        }
    });

    // ✅ SEARCH FUNCTIONALITY
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500);
        });
    }

    // ✅ INITIALIZE TOAST IF EXISTS
    const toastEl = document.getElementById('ticketSuccessToast');
    if (toastEl) {
        const ticketToast = new bootstrap.Toast(toastEl);
        ticketToast.show();
    }

    console.log('✅ Ticket List Page JavaScript initialized successfully');
});
</script>
@endpush