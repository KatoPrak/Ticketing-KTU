// ==========================================================
// it.js — FINAL & CONSOLIDATED (ALL FUNCTIONS INCLUDED)
// ==========================================================
import Swal from 'sweetalert2';

// ==========================
// BASE URL & HEADERS
// ==========================
const BASE_URL = "/it/tickets";
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || "";
const defaultHeaders = {
    "X-Requested-With": "XMLHttpRequest",
    "X-CSRF-TOKEN": csrfToken,
    "Accept": "application/json",
};

// ==========================
// HELPER FUNCTIONS
// ==========================
function getStatusBadgeClass(status) {
    if (!status) return "bg-light text-dark";
    const map = {
        waiting: "bg-secondary", in_progress: "bg-warning text-dark", pending: "bg-info text-dark",
        resolved: "bg-success", closed: "bg-danger",
    };
    return map[status.toLowerCase()] || "bg-light text-dark";
}

function getPriorityBadgeClass(priority) {
    if (!priority) return "bg-light text-dark";
    const map = {
        low: "bg-success", medium: "bg-info", high: "bg-warning", urgent: "bg-danger", critical: "bg-dark"
    };
    return map[priority.toLowerCase()] || "bg-light";
}

function formatEmptyData(value, defaultText = 'Not Available', defaultIcon = 'question-circle') {
    if (!value || value === '-' || value === 'N/A' || value === '' || value === null) {
        return `<i class="fas fa-${defaultIcon} me-1"></i>${defaultText}`;
    }
    return value;
}

function renderAttachments(attachments) {
    try {
        console.log("📎 [DEBUG] attachments raw data:", attachments);
        let files = attachments;
        // Robust parsing: Loop parsing if we get a string that looks like JSON
        while (typeof files === 'string' && files.trim() !== '') {
            if (files === 'null' || files === '[]') break;
            try {
                const parsed = JSON.parse(files);
                if (parsed === files) break; 
                files = parsed;
            } catch (e) {
                break;
            }
        }

        if (!files || !Array.isArray(files) || files.length === 0) {
            return '<span class="text-muted"><i class="fas fa-paperclip me-1"></i>No attachments</span>';
        }

        const cacheBuster = new Date().getTime();

        return '<div class="d-flex flex-wrap gap-3 mt-2">' + files.map(file => {
            if (typeof file !== 'string') return '';
            
            // Remove 'public/' prefix if exists (legacy check)
            const cleanPath = file.replace(/^public\//, '');
            const fileUrl = `/storage/${cleanPath}?t=${cacheBuster}`;
            const fileName = cleanPath.split("/").pop();
            const ext = fileName.split(".").pop().toLowerCase();
            
            // HEIC/HEIF are NOT natively supported in browsers, treat as files
            const isNativeImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);
            const isHEIC = ['heic', 'heif'].includes(ext);

            if (isNativeImage) {
                return `
                    <div class="attachment-wrapper">
                        <a href="${fileUrl}" target="_blank" class="d-block border rounded overflow-hidden shadow-sm hover-shadow transition" style="width: 110px; height: 110px; background: #eee;">
                            <img src="${fileUrl}" alt="Attachment" class="w-100 h-100" style="object-fit: cover;" onerror="this.onerror=null; this.src='https://placehold.co/110?text=Broken+Link';">
                        </a>
                    </div>
                `;
            } else {
                // Determine icon based on extension
                let icon = 'fa-file-alt';
                let color = 'text-secondary';
                let typeLabel = ext.toUpperCase();

                if (isHEIC) {
                    icon = 'fa-image';
                    color = 'text-info';
                    typeLabel = 'HEIC/HEIF';
                } else if (ext === 'pdf') {
                    icon = 'fa-file-pdf';
                    color = 'text-danger';
                } else if (['xlsx', 'xls', 'csv'].includes(ext)) {
                    icon = 'fa-file-excel';
                    color = 'text-success';
                } else if (['docx', 'doc'].includes(ext)) {
                    icon = 'fa-file-word';
                    color = 'text-primary';
                } else if (['pptx', 'ppt'].includes(ext)) {
                    icon = 'fa-file-powerpoint';
                    color = 'text-warning';
                } else if (['zip', 'rar', '7z'].includes(ext)) {
                    icon = 'fa-file-archive';
                    color = 'text-dark';
                }

                return `
                    <div class="attachment-wrapper">
                        <a href="${fileUrl}" target="_blank" class="btn btn-light btn-sm d-flex align-items-center justify-content-center border rounded shadow-sm hover-shadow transition p-0" style="width: 110px; height: 110px; flex-direction: column; background: #fcfcfc;">
                            <div class="d-flex align-items-center justify-content-center flex-grow-1 w-100">
                                <i class="fas ${icon} fa-3x ${color}"></i>
                            </div>
                            <div class="bg-light border-top w-100 py-1 px-2 text-center rounded-bottom">
                                <div class="text-truncate fw-bold text-dark" style="font-size: 9px;" title="${fileName}">${fileName}</div>
                                <div class="text-muted" style="font-size: 8px;">${typeLabel} File</div>
                            </div>
                        </a>
                    </div>
                `;
            }
        }).join("") + '</div>';
    } catch (err) {
        console.error("Attachment render error:", err);
        return '<span class="text-danger">Error loading attachments</span>';
    }
}

function updateSelectColor(select, status) {
    const statusTextClasses = {
        'waiting': 'text-secondary', 'in_progress': 'text-warning-emphasis',
        'pending': 'text-info-emphasis', 'resolved': 'text-success', 'closed': 'text-danger',
    };
    Object.values(statusTextClasses).forEach(cls => select.classList.remove(cls));
    select.classList.add(statusTextClasses[status] || 'text-dark');
}

// ==========================
// MAIN MODULE
// ==========================
document.addEventListener("DOMContentLoaded", () => {
    const body = document.body;

    // --- FORM SUBMISSION HANDLER (can be called from any page) ---
    const handleCreateTicketSubmit = async (e, reloadCallback) => {
        e.preventDefault();
        const createForm = e.target;
        const formData = new FormData(createForm);

        const submitBtn = createForm.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Loading...`;

        try {
            const res = await fetch(createForm.action, {
                method: "POST",
                headers: { "X-CSRF-TOKEN": csrfToken, "Accept": "application/json" },
                body: formData,
            });

            const data = await res.json();
            if (!res.ok) {
                const msg = data?.message || `Error: ${res.status}`;
                throw new Error(msg);
            }

            if (reloadCallback) {
                await reloadCallback(data.ticket);
            }

            createForm.reset();

            Swal.fire({
                title: "Tiket Berhasil Dibuat!",
                text: "Tiket kamu sudah tersimpan dan sedang diproses.",
                icon: "success",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "Oke"
            });

            const modalEl = document.getElementById("createTicketModal");
            if (modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            }

        } catch (err) {
            console.error("❌ Create ticket error:", err);
            Swal.fire({
                title: "Gagal Membuat Tiket",
                text: err.message,
                icon: "error",
                confirmButtonColor: "#d33",
                confirmButtonText: "Tutup"
            });
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `<i class="me-1"></i> Submit Ticket`;
        }
    };

    // ===============================
    // PAGE-SPECIFIC MODULES
    // ===============================
    if (body.classList.contains("page-it-tickets")) {
        console.log("🎫 IT Tickets Page Active");

        const tableBody = document.getElementById("ticketTableBody");
        const createForm = document.getElementById("createTicketModal");

        const appendTicketRow = (ticket) => {
            const emptyRow = tableBody.querySelector('.empty-row');
            if (emptyRow) emptyRow.remove();

            const row = document.createElement("tr");
            row.id = `ticket-row-${ticket.id}`;
            row.innerHTML = `
                <td>${ticket.ticket_id}</td>
                <td>${ticket.department?.name || "-"}</td>
                <td>${ticket.category?.name || "-"}</td>
                <td>${(ticket.description || '-').substring(0, 50)}...</td>
                <td><span class="badge ${getStatusBadgeClass(ticket.status)}">${ticket.status}</span></td>
                <td><span class="badge ${getPriorityBadgeClass(ticket.priority)}">${ticket.priority}</span></td>
                <td>${ticket.created_at_formatted || "-"}</td>
                <td><button class="btn btn-sm btn-outline-primary btn-detail-ticket" data-id="${ticket.id}"><i class="fas fa-eye"></i></button></td>`;
            if (tableBody) tableBody.prepend(row);
        };

        if (createForm) {
            createForm.addEventListener("submit", (e) => handleCreateTicketSubmit(e, appendTicketRow));
        }
    }

    if (body.classList.contains("page-staff-dashboard")) {
        console.log("📋 Staff Dashboard Active");

        const dashboardTableBody = document.getElementById("ticket-list-body");
        const createForm = document.getElementById("createTicketForm");

        const loadDashboardTickets = async () => {
            if (!dashboardTableBody) return;
            dashboardTableBody.innerHTML = `<tr><td colspan="4" class="text-center"><div class="spinner-border spinner-border-sm"></div></td></tr>`;

            try {
                const res = await fetch("/staff/fetch-dashboard-tickets", { headers: defaultHeaders });
                if (!res.ok) throw new Error(`Server responded with ${res.status}`);
                const tickets = await res.json();

                dashboardTableBody.innerHTML = "";
                if (tickets && tickets.length > 0) {
                    tickets.forEach(ticket => {
                        const row = document.createElement("tr");
                        row.innerHTML = `
                            <td><span class="fw-semibold text-dark">${ticket.ticket_id || '-'}</span></td>
                            <td>${(ticket.description || '-').substring(0, 40)}...</td>
                            <td><span class="badge ${getPriorityBadgeClass(ticket.priority)}">${ticket.priority || '-'}</span></td>
                            <td class="text-end"><span class="badge ${getStatusBadgeClass(ticket.status)}">${ticket.status || '-'}</span></td>`;
                        dashboardTableBody.appendChild(row);
                    });
                } else {
                    dashboardTableBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted">Tidak ada tiket terbaru.</td></tr>`;
                }
            } catch (err) {
                console.error("Dashboard fetch error:", err);
                dashboardTableBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Gagal memuat data.</td></tr>`;
            }
        };

        loadDashboardTickets();

        if (createForm) {
            createForm.addEventListener("submit", (e) => handleCreateTicketSubmit(e, loadDashboardTickets));
        }
    }

    // ==========================
    // LOGOUT BUTTON HANDLER
    // ==========================
    const logoutBtn = document.getElementById("logoutBtn");
    const logoutForm = document.getElementById("logoutForm");

    if (logoutBtn && logoutForm) {
        logoutBtn.addEventListener("click", (e) => {
            e.preventDefault();

            const locale = document.documentElement.lang || 'en';
            const message = locale.startsWith('id')
                ? 'Apakah Anda yakin ingin logout?'
                : 'Are you sure you want to logout?';

            if (confirm(message)) {
                logoutForm.submit();
            }
        });
    }

    // ==========================
    // TICKET DETAIL MODAL HANDLER - WITH TIMELINE SUPPORT
    // ==========================
    document.addEventListener("click", async (e) => {
        if (e.target.closest('.btn-detail-ticket')) {
            const button = e.target.closest('.btn-detail-ticket');
            const ticketId = button.dataset.id;

            if (ticketId) {
                await showTicketDetail(ticketId);
            }
        }
    });

    async function showTicketDetail(ticketId) {
        const modalEl = document.getElementById('detailTicketModal');
        if (!modalEl) {
            console.error('❌ Modal element not found');
            return;
        }

        // --- CLEAR PREVIOUS DATA TO PREVENT DOUBLING/LEAKAGE ---
        const idsToClear = [
            'd_ticket_id', 'd_user', 'd_location', 'd_department', 'd_category', 
            'd_description', 'd_attachments', 'd_resolution_attachments', 'd_notes', 'd_pending_notes', 
            'd_timeline_transfers', 'd_transferred_badge'
        ];
        idsToClear.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.innerHTML = '';
        });
        // Reset markers
        ['d_response_marker', 'd_pending_marker', 'd_resolved_marker'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.classList.remove('bg-warning', 'bg-success', 'bg-info');
                el.classList.add('bg-muted');
            }
        });

        let modal = bootstrap.Modal.getInstance(modalEl);
        if (!modal) {
            modal = new bootstrap.Modal(modalEl);
        }
        const loader = document.getElementById('d_loader');
        const content = document.getElementById('d_content');

        if (!loader || !content) {
            console.error('❌ Loader or content element not found');
            return;
        }

        // Show loader, hide content
        loader.classList.remove('d-none');
        content.classList.add('d-none');

        try {
            const response = await fetch(`/it/tickets/${ticketId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`Failed to fetch ticket details: ${response.status}`);
            }

            const ticket = await response.json();

            console.log('✅ Ticket data loaded:', ticket);

            // ✅ POPULATE BASIC INFO dengan icon
            const elementsToUpdate = {
                'd_ticket_id': formatEmptyData(ticket.ticket_id, 'Not Available', 'question-circle'),
                'd_user': formatEmptyData(ticket.user?.name, 'Unknown User', 'user-slash'),
                'd_location': formatEmptyData(ticket.user?.location, 'Unknown Location', 'map-marker-alt'), // ✅ Added location
                'd_department': formatEmptyData(ticket.department?.name, 'Not Specified', 'ban'),
                'd_category': formatEmptyData(ticket.category?.name, 'Not Specified', 'ban'),
                'd_description': formatEmptyData(ticket.description, 'No description provided', 'file-alt')
            };

            Object.entries(elementsToUpdate).forEach(([elementId, value]) => {
                const element = document.getElementById(elementId);
                if (element) {
                    element.innerHTML = value;
                }
            });

            // ✅ UPDATE TRANSFERRED BADGE
            const transferredBadge = document.getElementById('d_transferred_badge');
            if (transferredBadge) {
                // Check transfer logs to determine if it's a transferred ticket
                if (ticket.transfer_logs && ticket.transfer_logs.length > 0) {
                    // It has been transferred. Show current region.
                    // We need 'region' name from the ticket response. 
                    // Since I didn't add 'region' to IT TicketController response yet, I must assume I need to either add it there OR rely on the latest log's TO destination (which is risky if logs are missing but I just added them).
                    // Best is to use the `ticket.region.name` if I add it to the controller.
                    // Let's rely on transfer_logs last entry 'to' for now, OR better, let's update IT Controller to send region name.
                    // But wait, IT Controller sends `transfer_logs` with names.

                    // Simplify badge as requested: just "Transferred" without details
                    transferredBadge.innerHTML = `<span class="badge bg-warning text-dark">Transferred</span>`;
                } else if (ticket.assigned_to) {
                    // Determine if this assigned_to is a result of transfer or initial assignment?
                    // Initial assignment is not a transfer.
                    // So if no logs, empty.
                    transferredBadge.innerHTML = '';
                } else {
                    transferredBadge.innerHTML = '';
                }
            }

            // ✅ POPULATE TIMELINE DATES dengan icon
            const createdEl = document.getElementById('d_created');
            const responseEl = document.getElementById('d_response');
            const resolvedEl = document.getElementById('d_resolved');
            const pendingEl = document.getElementById('d_pending');
            const resolvedMarker = document.getElementById('d_resolved_marker');
            const resolvedTitle = document.getElementById('d_resolved_title');
            const responseMarker = document.getElementById('d_response_marker');
            const pendingMarker = document.getElementById('d_pending_marker');
            // REPORTED DATE
            if (createdEl) {
                const createdDate = ticket.created_at_formatted || ticket.created_at;
                createdEl.innerHTML = createdDate && createdDate !== 'N/A'
                    ? `<i class="fas fa-calendar-check me-1"></i>${createdDate}`
                    : `<i class="fas fa-calendar-times me-1"></i>Not recorded`;
            }

            // RESPONSE DATE
            if (responseEl && responseMarker) {
                const responseDate = ticket.response_at_formatted || ticket.updated_at;
                if (responseDate && responseDate !== 'Not yet' && responseDate !== 'N/A') {
                    responseEl.innerHTML = `<i class="fas fa-clock me-1"></i>${responseDate}`;
                    responseMarker.classList.remove('bg-muted');
                    responseMarker.classList.add('bg-warning');
                } else {
                    responseEl.innerHTML = `<i class="fas fa-hourglass-half me-1"></i>Waiting for response`;
                    responseMarker.classList.remove('bg-warning');
                    responseMarker.classList.add('bg-muted');
                }
            }

            // PENDING DATE
            if (pendingEl && pendingMarker) {
                const pendingDate = ticket.pending_at_formatted || ticket.pending_at;
                const timelinePending = document.getElementById('d_timeline_pending');
                if (pendingDate && pendingDate !== 'Not yet pending' && pendingDate !== 'N/A' && pendingDate !== '-') {
                    if (timelinePending) timelinePending.classList.remove('d-none');
                    pendingEl.innerHTML = `<i class="fas fa-clock me-1"></i>${pendingDate}`;
                    pendingMarker.classList.remove('bg-muted');
                    pendingMarker.classList.add('bg-warning');
                } else {
                    if (timelinePending) timelinePending.classList.add('d-none');
                    pendingEl.innerHTML = `<i class="fas fa-hourglass me-1"></i>Waiting for pending`;
                    pendingMarker.classList.remove('bg-warning');
                    pendingMarker.classList.add('bg-muted');
                }
            }

            // ✅ POPULATE TRANSFER TIMELINE
            const timelineTransfers = document.getElementById('d_timeline_transfers');
            if (timelineTransfers) {
                timelineTransfers.innerHTML = ''; // Clear previous
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
                        timelineTransfers.insertAdjacentHTML('beforeend', transferHtml);
                    });
                }
            }

            // RESOLVED DATE
            if (resolvedEl && resolvedMarker && resolvedTitle) {
                if (ticket.resolved_at_formatted &&
                    ticket.resolved_at_formatted !== 'Pending' &&
                    ticket.resolved_at_formatted !== '-' &&
                    ticket.resolved_at_formatted !== null &&
                    ticket.resolved_at_formatted !== 'N/A') {
                    // Ticket sudah resolved/closed
                    resolvedEl.innerHTML = `<i class="fas fa-check-double me-1"></i>${ticket.resolved_at_formatted}`;
                    resolvedMarker.classList.remove('bg-muted');
                    resolvedMarker.classList.add('bg-success');

                    const statusText = ticket.status === 'closed' ? 'Closed' : 'Solved';
                    resolvedTitle.innerHTML = `<i class="me-1"></i>${statusText}`;
                    resolvedTitle.style.color = '#198754'; // Success color
                } else {
                    // Ticket masih pending
                    resolvedEl.innerHTML = `<i class="fas fa-hourglass-half me-1"></i>Pending`;
                    resolvedMarker.classList.remove('bg-success');
                    resolvedMarker.classList.add('bg-muted');

                    resolvedTitle.innerHTML = `<i class="me-1"></i>Not Yet Solved`;
                    resolvedTitle.style.color = '#6c757d'; // Muted color
                }
            }

            // ✅ Handle resolution notes dengan icon
            const notesRow = document.getElementById('d_row_notes');
            const notesElement = document.getElementById('d_notes');
            if (notesRow && notesElement) {
                if (ticket.resolution_notes) {
                    const tempDiv = document.createElement('div');
                    tempDiv.textContent = ticket.resolution_notes;
                    let safeNotes = tempDiv.innerHTML;

                    const linkifiedNotes = safeNotes.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" class="text-primary text-decoration-underline" rel="noopener noreferrer">$1</a>');
                    notesElement.innerHTML = `<i class="fas fa-pen me-1"></i>${linkifiedNotes}`;
                    notesRow.classList.remove('d-none');
                } else {
                    notesRow.classList.add('d-none');
                }
            }

            // ✅ Handle pending notes (reason) dengan icon
            const pendingNotesRow = document.getElementById('d_row_pending_notes');
            const pendingNotesElement = document.getElementById('d_pending_notes');
            if (pendingNotesRow && pendingNotesElement) {
                if ((ticket.status && ticket.status.toLowerCase() === 'pending') && ticket.pending_reason) {
                    pendingNotesElement.innerHTML = `<i class="fas fa-pause-circle me-1"></i>${ticket.pending_reason}`;
                    pendingNotesRow.classList.remove('d-none');
                } else {
                    pendingNotesRow.classList.add('d-none');
                }
            }

            // ✅ Handle Transfer History
            const transfersRow = document.getElementById('d_row_transfers');
            const transfersElement = document.getElementById('d_transfers');
            if (transfersRow && transfersElement) {
                if (ticket.transfer_logs && ticket.transfer_logs.length > 0) {
                    const logsHtml = ticket.transfer_logs.map(log =>
                        `<div class="mb-2 p-2 bg-light border rounded">
                            <div class="fw-bold"><i class="fas fa-exchange-alt me-1 text-primary"></i> ${log.from} &rarr; ${log.to}</div>
                            ${log.note ? `<div class="text-muted small fst-italic mb-1"><i class="fas fa-quote-left me-1" style="font-size: 0.8em;"></i>${log.note}</div>` : ''}
                            <div class="text-muted" style="font-size: 0.85em;">
                                <i class="fas fa-user-clock me-1"></i> by ${log.by} on ${log.date}
                            </div>
                         </div>`
                    ).join('');
                    transfersElement.innerHTML = logsHtml;
                    transfersRow.classList.remove('d-none');
                } else {
                    transfersRow.classList.add('d-none');
                }
            }



            // ✅ Handle Staff attachments
            const attachmentsContainer = document.getElementById('d_attachments');
            if (attachmentsContainer) {
                attachmentsContainer.innerHTML = renderAttachments(ticket.attachments || []);
            }

            // ✅ Handle Resolution attachments
            const resAttachmentsContainer = document.getElementById('d_resolution_attachments');
            const resAttachmentsRow = document.getElementById('d_row_resolution_attachments');
            if (resAttachmentsContainer && resAttachmentsRow) {
                const resFiles = ticket.resolution_attachments || [];
                if (resFiles.length > 0) {
                    resAttachmentsContainer.innerHTML = renderAttachments(resFiles);
                    resAttachmentsRow.classList.remove('d-none');
                } else {
                    resAttachmentsRow.classList.add('d-none');
                }
            }

            // Hide loader, show content
            loader.classList.add('d-none');
            content.classList.remove('d-none');

            // Show modal
            modal.show();

        } catch (error) {
            console.error('❌ Error loading ticket details:', error);
            if (loader) {
                loader.innerHTML = `
                        < div class="alert alert-danger" >
                            <i class="fas fa-exclamation-triangle me-2"></i>
                        Failed to load ticket details: ${error.message}
                    </div >
                        `;
            }

            modal.show();
        }
    }

    // ==========================
    // HANDLER UPDATE STATUS/PRIORITY
    // ==========================
    let pendingUpdate = null;

    document.body.addEventListener('change', async (e) => {
        if (!e.target.classList.contains('update-ticket-field')) return;

        const select = e.target;
        const id = select.dataset.id;
        const field = select.dataset.field;
        const value = select.value;
        const old = select.dataset.originalValue;

        // ✅ Jika status berubah ke pending/resolved/closed, tampilkan modal
        if (field === 'status' && ['pending', 'resolved'].includes(value)) {
            pendingUpdate = {
                select: select,
                id: id,
                field: field,
                value: value,
                old: old
            };

            const modalEl = document.getElementById('resolutionModal');
            if (!modalEl) {
                console.error('❌ Resolution modal not found');
                select.value = old;
                return;
            }

            const modal = new bootstrap.Modal(modalEl);
            const notesTextarea = document.getElementById('resolutionNotes');
            if (notesTextarea) {
                notesTextarea.value = '';
                if (value === 'resolved') {
                    notesTextarea.placeholder = 'Write ticket completion notes...';
                } else {
                    notesTextarea.placeholder = 'Write ticket completion notes...';
                }
            }

            const attachmentsInput = document.getElementById('resolutionAttachments');
            if (attachmentsInput) {
                attachmentsInput.value = '';
            }

            const statusLabel = (value.charAt(0).toUpperCase() + value.slice(1).replace('_', ' ')).replace('Resolved', 'Solved');
            const modalTitle = modalEl.querySelector('.modal-title');
            if (modalTitle) {
                modalTitle.textContent = `Add Remark - ${statusLabel}`;
            }

            modal.show();
            return;
        }

        if (field === 'status' && value === 'closed') {
            Swal.fire({
                title: 'Are you sure?',
                text: "Ticket status will be changed to Closed!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    await updateFieldWithNotes(select, id, field, value, old, null);
                } else {
                    select.value = old;
                }
            });
            return;
        }

        await updateFieldWithNotes(select, id, field, value, old, null);
    });

    // ==========================
    // SAVE RESOLUTION NOTES BUTTON
    // ==========================
    const saveResolutionBtn = document.getElementById('saveResolutionBtn');
    if (saveResolutionBtn) {
        saveResolutionBtn.addEventListener('click', async () => {
            if (!pendingUpdate) {
                console.error('❌ No pending update data');
                return;
            }

            const notesTextarea = document.getElementById('resolutionNotes');
            const notes = notesTextarea.value.trim();
            const attachmentsInput = document.getElementById('resolutionAttachments');
            const files = attachmentsInput ? attachmentsInput.files : [];

            if (!notes && files.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Requirement Missing',
                    text: 'Please provide either a Remark or an Attachment before updating.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            // ➕ Add loading state
            const originalBtnText = saveResolutionBtn.innerHTML;
            saveResolutionBtn.disabled = true;
            saveResolutionBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';

            try {
                await updateFieldWithNotes(
                    pendingUpdate.select,
                    pendingUpdate.id,
                    pendingUpdate.field,
                    pendingUpdate.value,
                    pendingUpdate.old,
                    notes,
                    files
                );

                const modalEl = document.getElementById('resolutionModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) {
                    modal.hide();
                }

                pendingUpdate = null;

            } catch (err) {
                console.error("Save resolution error:", err);
                // Error handling is already done in updateFieldWithNotes, 
                // but we might want to ensure the button is reset here if updateFieldWithNotes throws
            } finally {
                // ➕ Remove loading state
                saveResolutionBtn.disabled = false;
                saveResolutionBtn.innerHTML = originalBtnText;
            }
        });
    }

    // ==========================
    // CANCEL MODAL
    // ==========================
    const resolutionModal = document.getElementById('resolutionModal');
    if (resolutionModal) {
        resolutionModal.addEventListener('hidden.bs.modal', () => {
            if (pendingUpdate && pendingUpdate.select) {
                pendingUpdate.select.value = pendingUpdate.old;
                pendingUpdate = null;
            }

            const notesTextarea = document.getElementById('resolutionNotes');
            const attachmentsInput = document.getElementById('resolutionAttachments');
            if (notesTextarea) {
                notesTextarea.value = '';
            }
            if (attachmentsInput) {
                attachmentsInput.value = '';
            }
        });
    }

    // ==========================
    // FUNCTION UPDATE FIELD WITH NOTES
    // ==========================
    async function updateFieldWithNotes(select, id, field, value, old, notes, files = []) {
        select.disabled = true;

        try {
            const formData = new FormData();
            formData.append('field', field);
            formData.append('value', value);

            if (notes) {
                formData.append('resolution_notes', notes);
            }

            if (files && files.length > 0) {
                const compressionOptions = {
                    maxSizeMB: 0.8,
                    maxWidthOrHeight: 1600,
                    useWebWorker: true,
                    initialQuality: 0.7
                };
                
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    if (file.type.startsWith('image/') && typeof imageCompression !== 'undefined') {
                        try {
                            const compressedFile = await imageCompression(file, compressionOptions);
                            const finalFile = new File([compressedFile], file.name, { type: file.type });
                            formData.append('attachments[]', finalFile);
                            console.log(`✅ Compressed ${file.name}: ${(file.size/1024/1024).toFixed(2)}MB -> ${(compressedFile.size/1024/1024).toFixed(2)}MB`);
                        } catch (error) {
                            console.error('❌ Compression error:', error);
                            formData.append('attachments[]', file);
                        }
                    } else {
                        formData.append('attachments[]', file);
                    }
                }
            }

            console.log('📤 Sending update (FormData)');

            const res = await fetch(`${BASE_URL}/${id}/update-field`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();

            console.log('📥 Response:', data);

            if (!res.ok) {
                let errorMsg = data.message || `Failed to update.Status: ${res.status} `;
                if (data.errors) {
                    errorMsg += '\n' + Object.values(data.errors).map(e => e.join(', ')).join('\n');
                }
                throw new Error(errorMsg);
            }

            select.dataset.originalValue = value;

            if (field === 'status') {
                updateSelectColor(select, value);

                if (value === 'closed') {
                    const row = select.closest('tr');
                    if (row) {
                        row.style.transition = 'all 0.5s ease';
                        row.style.opacity = '0';
                        setTimeout(() => {
                            row.remove();
                            // Optional: If no tickets left, maybe refresh or handle empty state
                            const tbody = document.querySelector('.ticket-row');
                            if (!tbody) {
                                window.location.reload();
                            }
                        }, 500);
                    }
                }
            }

            if (field === 'priority') {
                select.classList.remove('select-priority-low', 'select-priority-medium', 'select-priority-high', 'select-priority-urgent', 'select-priority-critical');
                select.classList.add(`select-priority-${value}`);
            }

            Swal.fire({
                icon: 'success',
                title: 'Updated!',
                text: data.message || 'Ticket updated successfully',
                timer: 2000,
                showConfirmButton: false
            });

        } catch (err) {
            console.error('❌ Update error:', err);

            select.value = old;

            Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                text: err.message,
                confirmButtonColor: '#d33'
            });
        } finally {
            select.disabled = false;
        }
    }
});