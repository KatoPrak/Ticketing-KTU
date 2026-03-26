<div class="modal fade" id="detailTicketModal" tabindex="-1" aria-labelledby="detailTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered custom-modal">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-ticket-alt me-2"></i> Ticket Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                {{-- Loader --}}
                <div id="d_loader" class="text-center py-4">
                    <div class="spinner-border text-info"></div>
                    <p class="text-muted mt-2">Loading ticket...</p>
                </div>

                {{-- Content --}}
                <div id="d_content" class="d-none">
                    <div class="row">
                        {{-- LEFT COLUMN: Ticket Info --}}
                        <div class="col-lg-7 mb-4 mb-lg-0" style="min-width: 0;">
                            <div class="ticket-info-section">
                                <table class="table table-borderless mb-0 ticket-detail-table">
                                    <tr>
                                        <th width="35%" class="text-muted">Ticket ID</th>
                                        <td>
                                            <span id="d_ticket_id" class="fw-bold text-primary">Not Available</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted align-top">Description</th>
                                        <td id="d_description" class="text-secondary text-break" style="word-wrap: break-word; word-break: break-all; white-space: pre-wrap; max-width: 200px;">No description provided</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Category</th>
                                        <td id="d_category" class="text-secondary text-break">Not Specified</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Department</th>
                                        <td id="d_department" class="text-secondary text-break">Not Specified</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Status</th>
                                        <td>
                                            <span id="d_status" class="badge rounded-pill px-3 py-2 bg-secondary">Unknown</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Priority</th>
                                        <td>
                                            <span id="d_priority" class="badge rounded-pill px-3 py-2 bg-secondary">Unknown</span>
                                        </td>
                                    </tr>
                                    <tr id="d_row_transfers" class="d-none">
                                        <th class="text-muted align-top">Transfer History</th>
                                        <td>
                                            <div id="d_transfers" class="text-muted small text-break"></div>
                                        </td>
                                    </tr>
                                    <tr id="d_row_pending_notes" class="d-none">
                                        <th class="text-muted align-top">Pending Reason</th>
                                        <td style="max-width: 200px;">
                                            <div id="d_pending_notes" class="text-muted fst-italic bg-warning p-2 rounded border border-warning" style="--bs-bg-opacity: .1; white-space: pre-wrap; word-break: break-all; overflow-wrap: anywhere;">No reason provided</div>
                                        </td>
                                    </tr>
                                    <tr id="d_row_notes" class="d-none">
                                        <th class="text-muted align-top">Resolution Notes</th>
                                        <td style="max-width: 200px;">
                                            <div id="d_notes" class="text-muted fst-italic bg-light p-2 rounded border" style="white-space: pre-wrap; word-break: break-all; overflow-wrap: anywhere;">No notes available</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted align-top">Attachments</th>
                                        <td id="d_attachments">
                                            <span class="text-muted">
                                                <i class="fas fa-paperclip me-1"></i>No attachments
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        {{-- RIGHT COLUMN: Timeline --}}
                        <div class="col-lg-5" style="min-width: 0;">
                            <div class="card bg-light border-0 timeline-card">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3 timeline-header">
                                        <i class="fas fa-clock me-2 text-info"></i>Timeline
                                    </h6>
                                    
                                    {{-- Timeline Container --}}
                                    <div class="timeline-container">
                                        {{-- REPORTED --}}
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-primary">
                                                <i class="fas fa-flag"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <div class="timeline-title text-primary fw-bold">Reported</div>
                                                <div class="timeline-date text-muted small" id="d_created">
                                                    <i class="fas fa-calendar-alt me-1"></i>Not recorded
                                                </div>
                                            </div>
                                        </div>

                                        {{-- RESPONSE --}}
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-warning" id="d_response_marker">
                                                <i class="fas fa-reply"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <div class="timeline-title text-warning fw-bold">Response</div>
                                                <div class="timeline-date text-muted small" id="d_response">
                                                    <i class="fas fa-hourglass-half me-1"></i>Waiting for response
                                                </div>
                                            </div>
                                        </div>

                                        {{-- PENDING --}}
                                        <div class="timeline-item" id="d_timeline_pending">
                                            <div class="timeline-marker bg-warning" id="d_pending_marker">
                                                <i class="fas fa-pause"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <div class="timeline-title text-warning fw-bold">Pending</div>
                                                <div class="timeline-date text-muted small" id="d_pending">
                                                    <i class="fas fa-hourglass me-1"></i>Waiting for pending
                                                </div>
                                            </div>
                                        </div>

                                        {{-- RESOLVED/CLOSED --}}
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-muted" id="d_resolved_marker"><i class="fas fa-check-circle"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <div class="timeline-title fw-bold" id="d_resolved_title" style="color: #6c757d;">Solved/Closed</div>
                                                <div class="timeline-date text-muted small" id="d_resolved"><i class="fas fa-clock me-1"></i>Not yet solved
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* ========================================
   TIMELINE STYLES
======================================== */
.timeline-container {
    position: relative;
    padding-left: 40px;
}

.timeline-container::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 10px;
    bottom: 10px;
    width: 2px;
    background: linear-gradient(to bottom, #0dcaf0, #198754);
}

.timeline-item {
    position: relative;
    margin-bottom: 28px;
    display: flex;
    align-items: flex-start;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -40px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    z-index: 2;
    transition: all 0.3s ease;
}

.timeline-marker.bg-muted {
    background-color: #6c757d !important;
    opacity: 0.6;
}

.timeline-content {
    flex: 1;
    padding-left: 8px;
}

.timeline-title {
    font-size: 14px;
    margin-bottom: 4px;
    font-weight: 600;
}

.timeline-date {
    font-size: 12px;
    line-height: 1.5;
}

.timeline-date i {
    opacity: 0.7;
}

/* ========================================
   MODAL CUSTOM WIDTH
======================================== */
.custom-modal {
    max-width: 900px;
}

/* ========================================
   TICKET DETAIL TABLE
======================================== */
.ticket-detail-table {
    table-layout: fixed;
    width: 100%;
}

.ticket-detail-table tr {
    border-bottom: 1px solid #f0f0f0;
}

.ticket-detail-table tr:last-child {
    border-bottom: none;
}

.ticket-detail-table th {
    padding: 12px 8px 12px 0;
    font-weight: 500;
    font-size: 14px;
    width: 30%; /* Force width */
    vertical-align: top;
}

.ticket-detail-table td {
    padding: 12px 0;
    font-size: 14px;
    vertical-align: top;
    word-wrap: break-word;
    word-break: break-all;
    overflow-wrap: anywhere;
}

/* ========================================
   TIMELINE CARD
======================================== */
.timeline-card {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.timeline-header {
    font-size: 15px;
    border-bottom: 2px solid #dee2e6;
    padding-bottom: 10px;
    margin-bottom: 20px !important;
}

/* ========================================
   MOBILE RESPONSIVE
======================================== */
@media (max-width: 768px) {
    .custom-modal {
        max-width: 95%;
        margin: 10px;
    }
    
    .modal-body {
        padding: 1rem;
    }
    
    /* Timeline untuk mobile */
    .timeline-container {
        margin-top: 20px;
        padding-left: 35px;
    }
    
    .timeline-container::before {
        left: 12px;
    }
    
    .timeline-marker {
        left: -35px;
        width: 28px;
        height: 28px;
        font-size: 12px;
    }
    
    .timeline-title {
        font-size: 13px;
    }
    
    .timeline-date {
        font-size: 11px;
    }
    
    /* Table responsive untuk mobile */
    .ticket-detail-table th {
        font-size: 12px;
        padding: 10px 5px 10px 0;
        width: 40% !important;
    }
    
    .ticket-detail-table td {
        font-size: 12px;
        padding: 10px 0;
    }
    
    /* Timeline card spacing */
    .timeline-card {
        margin-top: 15px;
    }
    
    .timeline-header {
        font-size: 14px;
    }
    
    /* Badge sizing untuk mobile */
    .badge {
        font-size: 11px !important;
        padding: 6px 10px !important;
    }
}

@media (max-width: 576px) {
    .custom-modal {
        max-width: 98%;
        margin: 5px;
    }
    
    .modal-body {
        padding: 0.75rem;
    }
    
    .ticket-detail-table th {
        width: 38% !important;
    }
    
    .timeline-container {
        padding-left: 30px;
    }
    
    .timeline-marker {
        width: 24px;
        height: 24px;
        font-size: 11px;
        left: -30px;
    }
}

/* ========================================
   PRINT STYLES (Optional)
======================================== */
@media print {
    .modal-header,
    .modal-footer {
        display: none;
    }
    
    .timeline-container::before {
        background: #000 !important;
    }
}
</style>