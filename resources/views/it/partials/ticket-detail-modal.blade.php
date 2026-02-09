
{{-- ✅ MODAL DETAIL WITH TIMELINE --}}
<div class="modal fade" id="detailTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 900px;">
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
                                        <th width="35%" class="text-muted">
                                            <i class="me-1"></i>Ticket ID
                                        </th>
                                        <td>
                                            <span id="d_ticket_id" class="fw-bold text-primary">
                                                <i class="me-1"></i>Not Available
                                            </span>
                                            <span id="d_transferred_badge" class="ms-2"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">
                                            <i class="me-1"></i>Name
                                        </th>
                                        <td id="d_user" class="text-secondary">
                                            <i class="me-1"></i>Unknown User
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">
                                            <i class="me-1"></i>Location
                                        </th>
                                        <td id="d_location" class="text-secondary">
                                            <i class="me-1"></i>Unknown Location
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">
                                            <i class="me-1"></i>Department
                                        </th>
                                        <td id="d_department" class="text-secondary">
                                            <i class="me-1"></i>Not Specified
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">
                                            <i class="me-1"></i>Category
                                        </th>
                                        <td id="d_category" class="text-secondary">
                                            <i class="me-1"></i>Not Specified
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted align-top">
                                            <i class="me-1"></i>Problem
                                        </th>
                                        <td>
                                            <div id="d_description" class="p-3 rounded bg-white border shadow-sm text-dark" style="white-space: pre-wrap; font-size: 0.95rem;">
                                                <i class="me-1"></i>No description provided
                                            </div>
                                        </td>
                                    </tr>
                                    <tr id="d_row_transfers" class="d-none">
                                        <th class="text-muted align-top">
                                            <i class="me-1"></i>Transfer History
                                        </th>
                                        <td>
                                            <div id="d_transfers" class="text-muted small"></div>
                                        </td>
                                    </tr>
                                    <tr id="d_row_notes" class="d-none">
                                        <th class="text-muted align-top">
                                            <i class="me-1"></i>Solution
                                        </th>
                                        <td>
                                            <div id="d_notes" class="text-muted fst-italic bg-light p-2 rounded border">
                                                <i class="me-1"></i>No notes available
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted align-top">
                                            <i class="me-1"></i>Attachments
                                        </th>
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
                                                    <i class="me-1"></i>Reported
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
                                                    <i class="me-1"></i>Response
                                                </div>
                                                <div class="timeline-date text-muted small" id="d_response">
                                                    <i class="fas fa-hourglass-me-1"></i>Waiting for response
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
                                                    <i class="me-1"></i>Not Yet Resolved/Closed
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>
