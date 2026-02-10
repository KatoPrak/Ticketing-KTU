// ============================================
// TICKET DETAIL MODAL HANDLER - STAFF VERSION WITH TIMELINE & RESPONSIVE
// ============================================

$(document).ready(function () {
    console.log('🔵 ticket-detail-handler.js loaded - Staff Version with Timeline');

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
        if (priorityLower === 'critical') return "bg-dark";
        return "bg-secondary";
    }

    function formatEmptyData(value, defaultText = 'Not Available') {
        if (!value || value === '-' || value === 'N/A' || value === '' || value === null) {
            return defaultText;
        }
        return value;
    }

    function renderAttachments(attachments) {
        try {
            // Robust parsing: Handle if API returns JSON string instead of array
            if (typeof attachments === 'string') {
                if (attachments.trim() === '' || attachments === 'null') return '<span class="text-muted"><i class="fas fa-paperclip me-1"></i>No attachments</span>';
                try {
                    attachments = JSON.parse(attachments);
                } catch (e) {
                    console.warn("Failed to parse attachments JSON string:", e);
                    return '<span class="text-danger">Invalid attachment format</span>';
                }
            }

            if (!attachments || !Array.isArray(attachments) || attachments.length === 0) {
                return '<span class="text-muted"><i class="fas fa-paperclip me-1"></i>No attachments</span>';
            }

            return '<div class="d-flex flex-wrap gap-2">' + attachments.map(file => {
                // Remove 'public/' prefix if exists (legacy check)
                const cleanPath = file.replace(/^public\//, '');
                const fileUrl = `/storage/${cleanPath}`;
                const fileName = cleanPath.split('/').pop();
                const isImage = /\.(jpg|jpeg|png|gif|webp|heic|heif)$/i.test(fileName);

                if (isImage) {
                    return `
                        <div class="attachment-item position-relative group">
                            <a href="${fileUrl}" target="_blank" class="d-block border rounded overflow-hidden shadow-sm transition-transform hover:scale-105" style="width: 100px; height: 100px;" title="${fileName}">
                                <img src="${fileUrl}" alt="${fileName}" class="w-100 h-100" style="object-fit: cover;">
                            </a>
                        </div>
                    `;
                } else {
                    return `
                        <div class="attachment-item">
                            <a href="${fileUrl}" target="_blank" class="btn btn-outline-light text-dark border d-flex align-items-center justify-content-center shadow-sm" style="width: 100px; height: 100px;" title="${fileName}">
                                <div class="text-center overflow-hidden w-100 px-1">
                                    <i class="fas fa-file-alt text-secondary mb-1 fa-2x"></i><br>
                                    <small class="d-block text-truncate w-100" style="font-size: 0.7rem;">${fileName}</small>
                                </div>
                            </a>
                        </div>
                    `;
                }
            }).join('') + '</div>';
        } catch (err) {
            console.error("Attachment render error:", err);
            return '<span class="text-danger">Error loading attachments</span>';
        }
    }

    // ========================
    // POPULATE MODAL WITH TIMELINE & INFORMATIVE TEXT
    // ========================
    function populateDetailModal(data) {
        console.log('✅ Populating modal with data:', data);

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

        // ✅ UPDATE BASIC INFO dengan teks informatif
        $('#d_ticket_id').text(formatEmptyData(ticket.ticket_id, 'Not Available'));
        $('#d_user').text(formatEmptyData(ticket.user?.name, 'Unknown User'));
        $('#d_department').text(formatEmptyData(ticket.department?.name, 'Not Specified'));
        $('#d_category').text(formatEmptyData(ticket.category?.name, 'Not Specified'));
        $('#d_description').text(formatEmptyData(ticket.description, 'No description provided'));

        // ✅ UPDATE TRANSFERRED BADGE (Region based)
        const transferredBadge = $('#d_transferred_badge');
        if (ticket.transfer_logs && ticket.transfer_logs.length > 0) {
            transferredBadge.html(`<span class="badge bg-warning text-dark">Transferred</span>`);
        } else {
            transferredBadge.empty();
        }

        // ✅ UPDATE TIMELINE DATES dengan teks informatif dan icon
        const createdEl = $('#d_created');
        const responseEl = $('#d_response');
        const resolvedEl = $('#d_resolved');
        const resolvedMarker = $('#d_resolved_marker');
        const resolvedTitle = $('#d_resolved_title');
        const responseMarker = $('#d_response_marker');

        // REPORTED DATE
        const createdDate = ticket.created_at_formatted || ticket.created_at;
        if (createdDate && createdDate !== 'N/A') {
            createdEl.html(`<i class="fas fa-calendar-check me-1"></i>${createdDate}`);
        } else {
            createdEl.html(`<i class="fas fa-calendar-alt me-1"></i>Not recorded`);
        }

        // RESPONSE DATE
        const responseDate = ticket.response_at_formatted || ticket.updated_at;
        if (responseDate && responseDate !== 'Not yet' && responseDate !== 'N/A') {
            responseEl.html(`<i class="fas fa-clock me-1"></i>${responseDate}`);
            if (responseMarker.length) {
                responseMarker.removeClass('bg-muted').addClass('bg-warning');
            }
        } else {
            responseEl.html(`<i class="fas fa-hourglass-half me-1"></i>Waiting for response`);
            if (responseMarker.length) {
                responseMarker.removeClass('bg-warning').addClass('bg-muted');
            }
        }

        // ✅ POPULATE TRANSFER TIMELINE
        const timelineTransfers = $('#d_timeline_transfers');
        if (timelineTransfers.length) {
            timelineTransfers.empty(); // Clear previous
            if (ticket.transfer_logs && ticket.transfer_logs.length > 0) {
                ticket.transfer_logs.forEach(log => {
                    const transferHtml = `
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-title text-info fw-bold">
                                    Transferred
                                </div>
                                <div class="text-dark small mb-1">
                                    <strong>${log.from}</strong> <i class="fas fa-arrow-right mx-1 text-muted"></i> <strong>${log.to}</strong>
                                </div>
                            </div>
                        </div>
                    `;
                    timelineTransfers.append(transferHtml);
                });
            }
        }

        // RESOLVED DATE - ✅ FIXED VERSION
        if (ticket.resolved_at_formatted &&
            ticket.resolved_at_formatted !== 'Pending' &&
            ticket.resolved_at_formatted !== '-' &&
            ticket.resolved_at_formatted !== null) {
            // Ticket sudah resolved/closed
            resolvedEl.html(`<i class="fas fa-check-double me-1"></i>${ticket.resolved_at_formatted}`);
            resolvedMarker.removeClass('bg-muted').addClass('bg-success');
            resolvedTitle.text(ticket.status === 'closed' ? 'Closed' : 'Resolved/Closed');
            resolvedTitle.removeClass('text-muted').addClass('text-success');
        } else {
            // Ticket masih pending - TAMPILKAN "Pending" dengan icon hourglass
            resolvedEl.html(`<i class="fas fa-hourglass-half me-1"></i>Pending`);
            resolvedMarker.removeClass('bg-success').addClass('bg-muted');
            resolvedTitle.text('Not Yet Resolved/Closed');
            resolvedTitle.removeClass('text-success').addClass('text-muted');
        }

        // ✅ HANDLE TRANSFER HISTORY
        const transfersRow = $('#d_row_transfers');
        const transfersElement = $('#d_transfers');
        if (ticket.transfer_logs && ticket.transfer_logs.length > 0) {
            const logsHtml = ticket.transfer_logs.map(log =>
                `<div class="mb-2 p-2 bg-light border rounded">
                    ${log.note ? `<div class="fw-bold text-dark mb-1">${log.note}</div>` : ''}
                    <div class="text-muted" style="font-size: 0.85em;">
                        <i class="fas fa-user me-1"></i> ${log.by}<br>
                        <i class="fas fa-clock me-1"></i> ${log.date}
                    </div>
                 </div>`
            ).join('');
            transfersElement.html(logsHtml);
            transfersRow.removeClass('d-none');
        } else {
            transfersRow.addClass('d-none');
        }

        // ✅ UPDATE RESOLUTION NOTES
        if (ticket.resolution_notes) {
            $('#d_notes').text(ticket.resolution_notes);
            $('#d_row_notes').removeClass('d-none');
        } else {
            $('#d_row_notes').addClass('d-none');
        }

        // ✅ UPDATE ATTACHMENTS
        $('#d_attachments').html(renderAttachments(ticket.attachments || []));
    }

    // ========================
    // SHOW ERROR IN MODAL
    // ========================
    function showErrorInModal(message) {
        $('#d_loader').addClass('d-none');
        $('#d_content').removeClass('d-none');

        $('#d_content').html(`
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${message}
            </div>
        `);
    }

    // ========================
    // RESET MODAL dengan teks default informatif
    // ========================
    function resetTicketModal() {
        $('#d_loader').removeClass('d-none');
        $('#d_content').addClass('d-none');

        // Reset text elements dengan teks informatif
        $('#d_ticket_id').text('Not Available');
        $('#d_user').text('Unknown User');
        $('#d_department').text('Not Specified');
        $('#d_category').text('Not Specified');
        $('#d_description').text('No description provided');

        // Reset timeline dengan teks informatif
        $('#d_created').html('<i class="fas fa-calendar-alt me-1"></i>Not recorded');
        $('#d_response').html('<i class="fas fa-hourglass-half me-1"></i>Waiting for response');
        $('#d_resolved').html('<i class="fas fa-clock me-1"></i>Not yet resolved');

        // Reset badges
        $('#d_status').removeClass().addClass('badge rounded-pill px-3 py-2 bg-secondary').text('UNKNOWN');
        $('#d_priority').removeClass().addClass('badge rounded-pill px-3 py-2 bg-secondary').text('UNKNOWN');

        // Reset timeline markers
        $('#d_resolved_marker').removeClass('bg-success').addClass('bg-muted');
        $('#d_resolved_title').removeClass('text-success').addClass('text-muted').text('Not Yet Resolved/Closed');

        if ($('#d_response_marker').length) {
            $('#d_response_marker').removeClass('bg-warning').addClass('bg-muted');
        }

        // Hide notes
        $('#d_row_notes').addClass('d-none');
        $('#d_notes').text('No notes available');

        // Reset attachments
        $('#d_attachments').html('<span class="text-muted"><i class="fas fa-paperclip me-1"></i>No attachments</span>');
    }

    // ========================
    // HANDLE DETAIL BUTTON CLICK
    // ========================
    $(document).on('click', '.btn-detail-ticket', function (e) {
        e.preventDefault();

        const ticketId = $(this).data('id');
        const modal = $('#detailTicketModal');

        console.log('🔄 Fetching ticket details for ID:', ticketId);

        // Reset modal state
        resetTicketModal();

        // Show modal
        modal.modal('show');

        // AJAX request
        $.ajax({
            url: `/staff/tickets/${ticketId}`,
            method: 'GET',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function (response) {
                console.log('✅ API Response:', response);

                if (response.success && response.ticket) {
                    populateDetailModal(response);
                } else {
                    showErrorInModal(response.message || 'Failed to load ticket details.');
                }
            },
            error: function (xhr, status, error) {
                console.error('❌ AJAX Error:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });

                let message = 'Failed to load ticket details.';

                if (xhr.status === 404) {
                    message = 'Ticket not found. It may have been deleted.';
                } else if (xhr.status === 403) {
                    message = 'You do not have permission to view this ticket.';
                } else if (xhr.status === 500) {
                    message = 'Server error. Please try again later or contact support.';
                } else if (xhr.status === 0) {
                    message = 'Network error. Please check your internet connection.';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                showErrorInModal(message);
            }
        });
    });

    // ========================
    // CLEAR MODAL ON HIDE
    // ========================
    $('#detailTicketModal').on('hidden.bs.modal', function () {
        console.log('📋 Modal hidden, resetting...');
        resetTicketModal();
    });

    // ========================
    // HANDLE MODAL SHOWN EVENT (Optional logging)
    // ========================
    $('#detailTicketModal').on('shown.bs.modal', function () {
        console.log('📋 Modal shown successfully');
    });

    console.log('🎯 Ticket detail handler with timeline & responsive design ready!');
});