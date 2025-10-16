// ============================================
// TICKET DETAIL MODAL HANDLER (FINAL VERSION)
// ============================================

$(document).ready(function() {

    // ========================
    // HANDLE DETAIL BUTTON CLICK
    // ========================
    $(document).on('click', '.btn-detail-ticket', function(e) {
        e.preventDefault();

        const ticketId = $(this).data('id');
        const modal = $('#detailTicketModal');

        // Reset modal
        resetTicketModal();

        // Show modal
        modal.modal('show');

        // Fetch ticket details via AJAX
        $.ajax({
            url: `/staff/tickets/${ticketId}`,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.ticket) {
                    populateTicketModal(response.ticket);
                } else {
                    showErrorInModal('Failed to load ticket details.');
                }
            },
            error: function(xhr) {
                let message = 'Failed to load ticket details.';

                // Cek Content-Type untuk pastikan JSON
                const isJson = xhr.getResponseHeader('Content-Type')?.includes('application/json');

                if (xhr.status === 404) {
                    message = 'Ticket not found.';
                } else if (xhr.status === 403) {
                    message = 'You do not have permission to view this ticket.';
                } else if (isJson && xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    console.error('Non-JSON response received:', xhr.responseText);
                    message = 'Failed to load ticket details (server returned HTML).';
                }

                showErrorInModal(message);
            }
        });
    });

    // ========================
    // RESET MODAL
    // ========================
    function resetTicketModal() {
        $('#d_loader').removeClass('d-none');
        $('#d_content').addClass('d-none');
        $('#d_content table').show(); // reset if previously error shown
        $('#d_ticket_id, #d_description, #d_category, #d_user, #d_created, #d_notes').text('-');
        $('#d_status, #d_priority').removeClass().addClass('badge rounded-pill px-3 py-2 bg-secondary').text('-');
        $('#d_attachments').html('<span class="text-muted">No attachments</span>');
        $('#d_row_notes').addClass('d-none');
    }

// ========================
// POPULATE MODAL (UPDATED)
// ========================
function populateTicketModal(ticket) {
    $('#d_loader').addClass('d-none');
    $('#d_content').removeClass('d-none');

    // Basic fields
    $('#d_ticket_id').text(ticket.ticket_id || '-');
    $('#d_description').text(ticket.description || '-');
    $('#d_category').text(ticket.category?.name || '-');
    $('#d_user').text(ticket.user?.name || '-');
    $('#d_created').text(ticket.created_at || '-');

    // Status badge
    const statusBadge = $('#d_status');
    statusBadge.text(ticket.status || 'Unknown');
    statusBadge.removeClass();
    switch (ticket.status?.toLowerCase()) {
        case 'open': statusBadge.addClass('badge rounded-pill px-3 py-2 bg-success'); break;
        case 'waiting': statusBadge.addClass('badge rounded-pill px-3 py-2 bg-secondary'); break;
        case 'progress':
        case 'in progress': statusBadge.addClass('badge rounded-pill px-3 py-2 bg-warning text-dark'); break;
        case 'resolved': statusBadge.addClass('badge rounded-pill px-3 py-2 bg-primary'); break;
        case 'closed': statusBadge.addClass('badge rounded-pill px-3 py-2 bg-danger'); break;
        default: statusBadge.addClass('badge rounded-pill px-3 py-2 bg-secondary');
    }

    // Priority badge
    const priorityBadge = $('#d_priority');
    priorityBadge.text(ticket.priority || 'Unknown');
    priorityBadge.removeClass();
    switch (ticket.priority?.toLowerCase()) {
        case 'high': priorityBadge.addClass('badge rounded-pill px-3 py-2 bg-danger'); break;
        case 'medium': priorityBadge.addClass('badge rounded-pill px-3 py-2 bg-warning text-dark'); break;
        case 'low': priorityBadge.addClass('badge rounded-pill px-3 py-2 bg-success'); break;
        default: priorityBadge.addClass('badge rounded-pill px-3 py-2 bg-secondary');
    }

    // Attachments (✨ image view support ✨)
    const attachmentsContainer = $('#d_attachments');
    attachmentsContainer.empty();

    if (ticket.attachments && ticket.attachments.length > 0) {
        ticket.attachments.forEach(file => {
            const fileUrl = file.startsWith('/storage/') ? file : `/storage/${file}`;
            const fileName = fileUrl.split('/').pop();
            const fileExt = fileName.split('.').pop().toLowerCase();

            // Tampilkan gambar langsung kalau formatnya image
if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic', 'heif'].includes(fileExt)) {
    attachmentsContainer.append(`
        <div class="attachment-wrapper">
            <a href="${fileUrl}" target="_blank">
                <img src="${fileUrl}" alt="${fileName}">
            </a>
        </div>
    `);
            } else {
                // File non-image tetap tampil sebagai link download
                let icon = 'fa-file';
                if (fileExt === 'pdf') icon = 'fa-file-pdf';
                else if (['doc', 'docx'].includes(fileExt)) icon = 'fa-file-word';
                else if (['xls', 'xlsx'].includes(fileExt)) icon = 'fa-file-excel';

                attachmentsContainer.append(`
                    <div class="mb-2">
                        <a href="${fileUrl}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas ${icon} me-1"></i> ${fileName}
                        </a>
                    </div>
                `);
            }
        });
    } else {
        attachmentsContainer.html('<span class="text-muted">No attachments</span>');
    }

    // Notes
    if (ticket.notes) {
        $('#d_notes').text(ticket.notes);
        $('#d_row_notes').removeClass('d-none');
    } else {
        $('#d_row_notes').addClass('d-none');
    }
}


    // ========================
    // SHOW ERROR IN MODAL
    // ========================
    function showErrorInModal(message) {
        $('#d_loader').addClass('d-none');
        $('#d_content').removeClass('d-none');
        $('#d_content table').hide(); // hide table if error
        $('#d_content').html(`
            <div class="alert alert-danger text-center" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${message}
            </div>
        `);
    }

    // ========================
    // CLEAR MODAL ON HIDE
    // ========================
    $('#detailTicketModal').on('hidden.bs.modal', function() {
        resetTicketModal();
    });

});
