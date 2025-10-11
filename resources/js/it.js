// ==========================
// it.js — Final, Clean & Globally Safe Version
// ==========================

import Swal from "sweetalert2";

// Base URL for IT routes
const BASE_URL = "/it/tickets";

// ==========================
// HELPER FUNCTIONS
// ==========================

/**
 * Gets the appropriate Bootstrap class for a ticket status.
 * @param {string} status The ticket status.
 * @returns {string} The Bootstrap class string.
 */
function getStatusBadgeClass(status) {
    if (!status) return 'bg-secondary-subtle text-secondary-emphasis';
    status = status.toLowerCase();
    const statusMap = {
        'open': 'bg-success-subtle text-success-emphasis',
        'waiting': 'bg-secondary-subtle text-secondary-emphasis',
        'in_progress': 'bg-warning-subtle text-warning-emphasis',
        'progress': 'bg-warning-subtle text-warning-emphasis', // Alias for in_progress
        'pending': 'bg-info-subtle text-info-emphasis',
        'resolved': 'bg-primary-subtle text-primary-emphasis',
        'closed': 'bg-danger-subtle text-danger-emphasis'
    };
    return statusMap[status] || 'bg-secondary-subtle text-secondary-emphasis';
}

/**
 * Gets the appropriate Bootstrap class for a ticket priority.
 * @param {string} priority The ticket priority.
 * @returns {string} The Bootstrap class string.
 */
function getPriorityBadgeClass(priority) {
    if (!priority) return 'bg-secondary-subtle text-secondary-emphasis';
    priority = priority.toLowerCase();
    const priorityMap = {
        'high': 'bg-danger-subtle text-danger-emphasis',
        'urgent': 'bg-danger-subtle text-danger-emphasis',
        'medium': 'bg-warning-subtle text-warning-emphasis',
        'low': 'bg-success-subtle text-success-emphasis'
    };
    return priorityMap[priority] || 'bg-secondary-subtle text-secondary-emphasis';
}

/**
 * Renders ticket attachments as HTML.
 * @param {string} attachments JSON string of attachment paths.
 * @returns {string} HTML content for attachments.
 */
function renderAttachments(attachments) {
    try {
        // Attachments might be a string or already an object/array
        const files = typeof attachments === 'string' ? JSON.parse(attachments) : attachments;
        if (!files || !files.length) return "No attachments.";

        return files
            .map((file) => {
                const ext = file.split(".").pop().toLowerCase();
                const isImage = ["jpg", "jpeg", "png", "gif", "webp", "heif"].includes(ext);
                const fileUrl = `/storage/${file}`;
                const fileName = file.split("/").pop();

                if (isImage) {
                    return `
                        <div class="mb-2">
                            <a href="${fileUrl}" target="_blank">
                                <img src="${fileUrl}" alt="Attachment" class="img-thumbnail" style="max-height:150px;">
                            </a>
                        </div>`;
                } else {
                    return `
                        <div class="mb-2">
                            <a href="${fileUrl}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-paperclip me-1"></i> ${fileName}
                            </a>
                        </div>`;
                }
            })
            .join("");
    } catch (err) {
        console.error("Attachment parse error:", err);
        return "Error loading attachments.";
    }
}
window.renderAttachments = renderAttachments; // Make available globally if needed

/**
 * Shows a toast notification using SweetAlert2.
 * @param {string} icon 'success', 'error', 'warning', 'info'.
 * @param {string} title The message to display.
 */
function showToast(icon, title) {
    Swal.fire({
        toast: true,
        icon: icon,
        title: title,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });
}
window.showToast = showToast;

// ==========================
// EVENT DELEGATION FOR CLICKS
// ==========================

document.addEventListener("click", async (e) => {
    
    // --- Detail Ticket Modal Logic ---
    const detailBtn = e.target.closest(".btn-detail-ticket");
    if (detailBtn) {
        const ticketId = detailBtn.dataset.id;
        if (!ticketId) return;

        const modal = new bootstrap.Modal(document.getElementById("detailTicketModal"));
        const loader = document.getElementById("d_loader");
        const content = document.getElementById("d_content");

        loader.classList.remove("d-none");
        content.classList.add("d-none");
        modal.show();

        try {
            const res = await fetch(`${BASE_URL}/${ticketId}`);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const ticket = await res.json();

            // Populate modal data
            document.getElementById("d_ticket_id").innerText = ticket.ticket_id || "-";
            document.getElementById("d_description").innerText = ticket.description || "-";
            document.getElementById("d_category").innerText = ticket.category?.name || "-";
            document.getElementById("d_user").innerText = ticket.user?.name || "-";
            document.getElementById("d_department").innerText = ticket.user?.department || "-";
            document.getElementById("d_created").innerText = ticket.created_at || "-";

            // Populate and color status badge
            const statusBadge = document.getElementById("d_status");
            statusBadge.innerText = ticket.status || "-";
            statusBadge.className = `badge rounded-pill px-3 py-2 ${getStatusBadgeClass(ticket.status)}`;

            // Populate and color priority badge
            const priorityBadge = document.getElementById("d_priority");
            priorityBadge.innerText = ticket.priority || "-";
            priorityBadge.className = `badge rounded-pill px-3 py-2 ${getPriorityBadgeClass(ticket.priority)}`;

            // ✅ RENDER ATTACHMENTS
            const attachContainer = document.getElementById("d_attachments");
            attachContainer.innerHTML = renderAttachments(ticket.attachments);

            // ✅ RENDER RESOLUTION NOTES
            const notesRow = document.getElementById('d_row_notes');
            const notesDiv = document.getElementById('d_notes');
            if (ticket.resolution_notes && ticket.resolution_notes.trim() !== '') {
                notesDiv.innerText = ticket.resolution_notes;
                notesRow.classList.remove('d-none');
            } else {
                notesDiv.innerText = '';
                notesRow.classList.add('d-none');
            }

        } catch (err) {
            console.error("Failed to load ticket details:", err);
            showToast("error", "Failed to load ticket details");
            modal.hide();
        } finally {
            loader.classList.add("d-none");
            content.classList.remove("d-none");
        }
    }

    // --- Delete Ticket Logic ---
    const deleteBtn = e.target.closest(".btn-delete-ticket");
    if (deleteBtn) {
        const ticketId = deleteBtn.dataset.id;
        if (!ticketId) return;

        const confirm = await Swal.fire({
            title: "Delete this ticket?",
            text: "This action cannot be undone!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete it",
            cancelButtonText: "Cancel",
        });

        if (!confirm.isConfirmed) return;

        try {
            const res = await fetch(`${BASE_URL}/${ticketId}`, {
                method: "DELETE",
                headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content },
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            showToast("success", "Ticket deleted successfully");
            document.getElementById(`ticket-row-${ticketId}`)?.remove();
        } catch (err) {
            console.error("Failed to delete ticket:", err);
            showToast("error", "Failed to delete ticket");
        }
    }

    // --- Sidebar Overlay Click ---
    if (e.target.id === "sidebarOverlay") {
        closeSidebar();
    }
});


// ==========================
// GLOBAL FUNCTIONS (SIDEBAR, ETC.)
// ==========================

function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebarOverlay");
    if (!sidebar || !overlay) return;

    const isVisible = sidebar.classList.toggle("show");
    overlay.classList.toggle("show", isVisible);
    document.body.classList.toggle("sidebar-open", isVisible);
}
window.toggleSidebar = toggleSidebar;

function closeSidebar() {
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebarOverlay");
    if (!sidebar || !overlay) return;

    sidebar.classList.remove("show");
    overlay.classList.remove("show");
    document.body.classList.remove("sidebar-open");
}
window.closeSidebar = closeSidebar;

// Close sidebar on ESC key
document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeSidebar();
});