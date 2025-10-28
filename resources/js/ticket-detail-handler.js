// ============================================
// TICKET DETAIL MODAL HANDLER - MATCHED WITH CONTROLLER
// ============================================

$(document).ready(function() {
    console.log('🔵 ticket-detail-handler.js loaded - Staff Version');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || "";

    // ========================
    // HELPER FUNCTIONS
    // ========================
    function getStatusBadgeClass(status) {
        if (!status) return "bg-secondary";
        const statusLower = status.toLowerCase();
        if (statusLower === 'waiting') return "bg-secondary";
        if (statusLower === 'in_progress' || statusLower === 'progress') return "bg-warning text-dark";
        if (statusLower === 'pending') return "bg-info text-dark";
        if (statusLower === 'resolved') return "bg-success";
        if (statusLower === 'closed') return "bg-danger";
        return "bg-secondary";
    }

    function getPriorityBadgeClass(priority) {
        if (!priority) return "bg-secondary";
        const priorityLower = priority.toLowerCase();
        if (priorityLower === 'low') return "bg-success";
        if (priorityLower === 'medium') return "bg-info";
        if (priorityLower === 'high') return "bg-warning";
        if (priorityLower === 'urgent') return "bg-danger";
        return "bg-secondary";
    }

    function renderAttachments(attachments) {
        try {
            if (!attachments || attachments.length === 0) {
                return '<span class="text-muted">No attachments</span>';
            }
            
            return attachments.map(file => {
                const fileUrl = `/storage/${file}`;
                const fileName = file.split('/').pop();
                const isImage = /\.(jpg|jpeg|png|gif|webp|heic|heif)$/i.test(fileName);
                
                if (isImage) {
                    return `
                        <div class="mb-2">
                            <a href="${fileUrl}" target="_blank" class="d-inline-block">
                                <img src="${fileUrl}" alt="${fileName}" class="img-thumbnail" style="max-height: 100px; max-width: 150px;">
                            </a>
                        </div>
                    `;
                } else {
                    return `
                        <div class="mb-2">
                            <a href="${fileUrl}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download me-1"></i> ${fileName}
                            </a>
                        </div>
                    `;
                }
            }).join('');
        } catch (err) {
            console.error("Attachment render error:", err);
            return '<span class="text-danger">Error loading attachments</span>';
        }
    }

    // ========================
    // POPULATE MODAL - MATCHED WITH CONTROLLER RESPONSE
    // ========================
    function populateDetailModal(data) {
        console.log('Populating modal with data:', data);
        
        const content = $('#d_content');
        const loader = $('#d_loader');
        
        // Hide loader, show content
        loader.addClass('d-none');
        content.removeClass('d-none');
        
        if (!data.success || !data.ticket) {
            content.html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${data.message || 'Failed to load ticket details'}
                </div>
            `);
            return;
        }

        const ticket = data.ticket;
        
        // Build HTML content based on controller response structure
        let html = `
            <table class="table table-borderless">
                <tr>
                    <th width="30%">Ticket ID</th>
                    <td>${ticket.ticket_id || '-'}</td>
                </tr>
                <tr>
                    <th>User</th>
                    <td>${ticket.user?.name || '-'}</td>
                </tr>
                <tr>
                    <th>Department</th>
                    <td>${ticket.department?.name || '-'}</td>
                </tr>
                <tr>
                    <th>Category</th>
                    <td>${ticket.category?.name || '-'}</td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td style="white-space: pre-wrap; background: #f8f9fa; padding: 10px; border-radius: 5px;">${ticket.description || '-'}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge rounded-pill px-3 py-2 ${getStatusBadgeClass(ticket.status)}">
                            ${ticket.status ? ticket.status.charAt(0).toUpperCase() + ticket.status.slice(1) : '-'}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Priority</th>
                    <td>
                        <span class="badge rounded-pill px-3 py-2 ${getPriorityBadgeClass(ticket.priority)}">
                            ${ticket.priority ? ticket.priority.charAt(0).toUpperCase() + ticket.priority.slice(1) : '-'}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>${ticket.created_at || '-'}</td>
                </tr>
                <tr>
                    <th>Last Updated</th>
                    <td>${ticket.updated_at || '-'}</td>
                </tr>
                <tr>
                    <th>Attachments</th>
                    <td>${renderAttachments(ticket.attachments || [])}</td>
                </tr>
        `;

        // Add notes if available (from controller response)
        if (ticket.notes || ticket.resolution_notes) {
            html += `
                <tr>
                    <th>Notes/Resolution</th>
                    <td style="white-space: pre-wrap; background: #fff3cd; padding: 10px; border-radius: 5px;">
                        ${ticket.notes || ticket.resolution_notes || ''}
                    </td>
                </tr>
            `;
        }

        html += `</table>`;
        
        content.html(html);
    }

    // ========================
    // SHOW ERROR IN MODAL
    // ========================
    function showErrorInModal(message) {
        $('#d_loader').addClass('d-none');
        $('#d_content').removeClass('d-none').html(`
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${message}
            </div>
        `);
    }

    // ========================
    // RESET MODAL
    // ========================
    function resetTicketModal() {
        $('#d_loader').removeClass('d-none');
        $('#d_content').addClass('d-none').html('');
        $('#d_ticket_id, #d_description, #d_category, #d_user, #d_created, #d_notes').text('-');
        $('#d_status, #d_priority').removeClass().addClass('badge bg-secondary').text('-');
    }

    // ========================
    // HANDLE DETAIL BUTTON CLICK
    // ========================
    $(document).on('click', '.btn-detail-ticket', function(e) {
        e.preventDefault();
        
        const ticketId = $(this).data('id');
        const modal = $('#detailTicketModal');
        
        console.log('🔄 Fetching ticket details for ID:', ticketId);

        // Reset modal state
        resetTicketModal();
        
        // Show modal
        modal.modal('show');

        // AJAX request to match controller
        $.ajax({
            url: `/staff/tickets/${ticketId}`,
            method: 'GET',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                console.log('✅ API Response:', response);
                
                if (response.success && response.ticket) {
                    populateDetailModal(response);
                } else {
                    showErrorInModal(response.message || 'Failed to load ticket details.');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ AJAX Error:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });

                let message = 'Failed to load ticket details.';
                
                if (xhr.status === 404) {
                    message = 'Ticket not found.';
                } else if (xhr.status === 403) {
                    message = 'You do not have permission to view this ticket.';
                } else if (xhr.status === 500) {
                    message = 'Server error. Please try again later.';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else {
                    // Try to parse HTML response for Laravel errors
                    try {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(xhr.responseText, 'text/html');
                        const errorTitle = doc.querySelector('title');
                        if (errorTitle) {
                            message = `Error: ${errorTitle.textContent}`;
                        }
                    } catch (e) {
                        console.log('Could not parse error response');
                    }
                }

                showErrorInModal(message);
            }
        });
    });

    // ========================
    // CLEAR MODAL ON HIDE
    // ========================
    $('#detailTicketModal').on('hidden.bs.modal', function() {
        resetTicketModal();
    });

    // ========================
    // DEBUG: Test function
    // ========================
    window.debugTicket = function(ticketId = 326) {
        console.log('🧪 Testing ticket fetch...');
        $.ajax({
            url: `/staff/tickets/${ticketId}`,
            method: 'GET',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('🧪 Debug Response:', response);
                if (response.success) {
                    console.log('✅ Ticket Data:', response.ticket);
                } else {
                    console.log('❌ Error:', response.message);
                }
            },
            error: function(xhr) {
                console.log('❌ Debug Error:', xhr.status, xhr.responseText);
            }
        });
    };

    console.log('🎯 Ticket detail handler ready. Use debugTicket(326) to test.');
});
