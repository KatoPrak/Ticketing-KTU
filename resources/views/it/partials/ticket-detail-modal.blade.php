
{{-- ✅ MODAL DETAIL WITH TIMELINE --}}
<div class="modal fade" id="detailTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" style="max-width: 900px;">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-ticket-alt me-2"></i>Ticket Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                
                {{-- Loader --}}
                <div id="d_loader" class="text-center py-4">
                    <div class="spinner-border text-info"></div>
                    <p class="text-muted mt-2">
                        <i class="fas fa-sync-alt fa-spin me-1"></i>Loading ticket without sync...
                    </p>
                </div>

                {{-- Content with Timeline --}}
                <div id="d_content" class="d-none">
                    <div class="row">
                        {{-- LEFT COLUMN: Ticket Info --}}
                        <div class="col-md-7 mb-4 mb-md-0">
                            <div class="ticket-info-section">
                                <table class="table table-borderless mb-0 ticket-detail-table">
                                    <tr>
                                        <th class="text-muted ticket-label">
                                            <i class="fas fa-hashtag me-1"></i>Ticket ID
                                        </th>
                                        <td>
                                            <span id="d_ticket_id" class="fw-bold text-primary">
                                                Not Available
                                            </span>
                                            <span id="d_transferred_badge" class="ms-2"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted ticket-label">
                                            <i class="fas fa-user me-1"></i>Name
                                        </th>
                                        <td id="d_user" class="text-secondary">
                                            Unknown User
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted ticket-label">
                                            <i class="fas fa-map-marker-alt me-1"></i>Location
                                        </th>
                                        <td id="d_location" class="text-secondary">
                                            Unknown Location
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted ticket-label">
                                            <i class="fas fa-building me-1"></i>Department
                                        </th>
                                        <td id="d_department" class="text-secondary">
                                            Not Specified
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted ticket-label">
                                            <i class="fas fa-tag me-1"></i>Category
                                        </th>
                                        <td id="d_category" class="text-secondary">
                                            Not Specified
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted align-top ticket-label">
                                            <i class="fas fa-exclamation-circle me-1"></i>Problem
                                        </th>
                                        <td>
                                            <div id="d_description" class="p-3 rounded bg-white border shadow-sm text-dark ticket-description">
                                                No description provided
                                            </div>
                                        </td>
                                    </tr>
                                    <tr id="d_row_transfers" class="d-none">
                                        <th class="text-muted align-top ticket-label">
                                            <i class="fas fa-exchange-alt me-1"></i>Transfer History
                                        </th>
                                        <td>
                                            <div id="d_transfers" class="text-muted small text-break"></div>
                                        </td>
                                    </tr>
                                    <tr id="d_row_notes" class="d-none">
                                        <th class="text-muted align-top ticket-label">
                                            <i class="fas fa-lightbulb me-1"></i>Solution
                                        </th>
                                        <td>
                                            <div id="d_notes" class="text-muted fst-italic bg-light p-2 rounded border text-break">
                                                No notes available
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted align-top ticket-label">
                                            <i class="fas fa-paperclip me-1"></i>Attachments
                                        </th>
                                        <td id="d_attachments">
                                            <span class="text-muted">
                                                No attachments
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        {{-- RIGHT COLUMN: Timeline --}}
                        <div class="col-md-5">
                            <div class="card bg-light border-0 timeline-card">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3 timeline-header">
                                        <i class="fas fa-history me-2 text-info"></i>Timeline
                                    </h6>
                                    
                                    {{-- Timeline Container --}}
                                    <div class="timeline-container">
                                        {{-- REPORTED --}}
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-primary">
                                                <i class="fas fa-flag"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <div class="timeline-title text-primary fw-bold">
                                                    Reported
                                                </div>
                                                <div class="timeline-date text-muted small" id="d_created">
                                                    <i class="fas fa-calendar-times me-1"></i>Not recorded
                                                </div>
                                            </div>
                                        </div>

                                        {{-- ✅ TRANSFERS (Dynamic) --}}
                                        <div id="d_timeline_transfers"></div>

                                        {{-- RESPONSE --}}
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-warning" id="d_response_marker">
                                                <i class="fas fa-reply"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <div class="timeline-title text-warning fw-bold">
                                                    Response
                                                </div>
                                                <div class="timeline-date text-muted small" id="d_response">
                                                    <i class="fas fa-hourglass me-1"></i>Waiting for response
                                                </div>
                                            </div>
                                        </div>

                                        {{-- RESOLVED/CLOSED --}}
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-muted" id="d_resolved_marker">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <div class="timeline-title fw-bold" id="d_resolved_title" style="color: #6c757d;">
                                                    Not Yet Resolved/Closed
                                                </div>
                                                <div class="timeline-date text-muted small" id="d_resolved">
                                                    <i class="fas fa-hourglass-half me-1"></i>Pending
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-warning d-none btn-transfer-ticket" id="modal_btn_transfer">
                    <i class="fas fa-exchange-alt me-1"></i>Transfer
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Mobile Responsive Styles for Ticket Detail Modal */
@media (max-width: 767.98px) {
    /* Modal adjustments */
    #detailTicketModal .modal-dialog {
        margin: 0.5rem;
        max-width: calc(100% - 1rem) !important;
    }
    
    #detailTicketModal .modal-body {
        padding: 1rem;
    }
    
    /* Stack table layout on mobile */
    .ticket-detail-table {
        display: block;
    }
    
    .ticket-detail-table tr {
        display: flex;
        flex-direction: column;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .ticket-detail-table tr:last-child {
        border-bottom: none;
    }
    
    .ticket-detail-table th,
    .ticket-detail-table td {
        display: block;
        width: 100% !important;
        padding: 0.25rem 0 !important;
    }
    
    .ticket-detail-table th.ticket-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 0.25rem;
    }
    
    .ticket-detail-table td {
        font-size: 0.9rem;
    }
    
    /* Description box */
    .ticket-description {
        font-size: 0.85rem !important;
        padding: 0.75rem !important;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    
    /* Timeline card on mobile */
    .timeline-card {
        margin-top: 1.5rem;
    }
    
    .timeline-header {
        font-size: 1rem;
    }
    
    /* Timeline items - more compact */
    .timeline-item {
        padding-left: 2rem;
        margin-bottom: 1.25rem;
        position: relative;
    }
    
    .timeline-marker {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        left: 0;
        top: 0;
        color: white;
        font-size: 0.75rem;
    }
    
    .timeline-content {
        padding-left: 0.5rem;
    }
    
    .timeline-title {
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }
    
    .timeline-date {
        font-size: 0.75rem !important;
    }
    
    /* Timeline connector line */
    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 13px;
        top: 28px;
        bottom: -1.25rem;
        width: 2px;
        background: #dee2e6;
    }
    
    /* Modal footer buttons */
    #detailTicketModal .modal-footer {
        flex-direction: column-reverse;
        gap: 0.5rem;
    }
    
    #detailTicketModal .modal-footer .btn {
        width: 100%;
        margin: 0 !important;
    }
}

/* Desktop styles */
@media (min-width: 768px) {
    .ticket-detail-table {
        table-layout: fixed;
    }
    
    .ticket-detail-table th {
        width: 35%;
    }
    
    .ticket-description {
        white-space: pre-wrap;
        font-size: 0.95rem;
    }
    
    /* Timeline styling for desktop */
    .timeline-item {
        display: flex;
        margin-bottom: 1.5rem;
        position: relative;
    }
    
    .timeline-marker {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-right: 1rem;
        color: white;
    }
    
    .timeline-content {
        flex: 1;
    }
    
    .timeline-title {
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }
    
    .timeline-date {
        font-size: 0.85rem;
    }
    
    /* Timeline connector */
    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 32px;
        bottom: -1.5rem;
        width: 2px;
        background: #dee2e6;
    }
}

/* Common styles */
.bg-muted {
    background-color: #6c757d !important;
}

#detailTicketModal .modal-header {
    border-bottom: none;
}

#detailTicketModal .modal-footer {
    border-top: 1px solid #dee2e6;
}
</style>


