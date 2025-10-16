<div class="modal fade" id="detailTicketModal" tabindex="-1" aria-labelledby="detailTicketModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered custom-modal">
        <div class="modal-content shadow-lg border-0">

            {{-- Header --}}
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-ticket-alt me-2"></i> Ticket Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body">

                {{-- Loader --}}
                <div id="d_loader" class="text-center py-4">
                    <div class="spinner-border text-info"></div>
                    <p class="text-muted mt-2">Loading ticket...</p>
                </div>

                {{-- Content --}}
                <div id="d_content" class="d-none">
                    <table class="table table-borderless">
                        <tr>
                            <th>Ticket ID</th>
                            <td id="d_ticket_id">-</td>
                        </tr>
                        <tr>
                            <th>Department</th>
                            <td id="d_department">-</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td id="d_description" style="white-space: pre-wrap;">-</td>
                        </tr>
                        <tr>
                            <th>Category</th>
                            <td id="d_category">-</td>
                        </tr>
                        <tr>
                            <th>User</th>
                            <td id="d_user">-</td>
                        </tr>
                        <tr>
                            <th>Created</th>
                            <td id="d_created">-</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span id="d_status" class="badge rounded-pill px-3 py-2 bg-secondary">-</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Priority</th>
                            <td>
                                <span id="d_priority" class="badge rounded-pill px-3 py-2 bg-secondary">-</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Attachments</th>
                            <td id="d_attachments">
                                <span class="text-muted">No attachments</span>
                            </td>
                        </tr>
                        <tr id="d_row_notes" class="d-none">
                            <th>Notes</th>
                            <td id="d_notes">-</td>
                        </tr>
                    </table>
                </div>

            </div>
