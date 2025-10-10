<div class="modal fade" id="showTicketModal" tabindex="-1" aria-labelledby="showTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="showTicketModalLabel">
                    <i class="fas fa-ticket-alt me-2"></i> Ticket Detail
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>Ticket ID:</strong> <span id="modalTicketId" class="text-muted"></span>
                </div>
                <div class="mb-3">
                    <strong>Category:</strong> <span id="modalCategory" class="text-muted"></span>
                </div>
                <div class="mb-3">
                    <strong>Description:</strong>
                    <p id="modalDescription" class="text-muted mb-0"></p>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <strong>Status:</strong>
                        <span id="modalStatus" class="badge bg-secondary"></span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Priority:</strong>
                        <span id="modalPriority" class="badge bg-secondary"></span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Created:</strong>
                        <span id="modalCreated" class="text-muted"></span>
                    </div>
                </div>
                <div class="mb-2">
                    <strong>Attachments:</strong>
                    <div id="modalAttachments" class="d-flex flex-wrap gap-2 mt-2"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>
