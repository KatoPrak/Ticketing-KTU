// ==========================================================
// it.js — FINAL & CONSOLIDATED (ALL FUNCTIONS INCLUDED)
// ==========================================================
import Swal from 'sweetalert2';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'bootstrap/dist/css/bootstrap.min.css';


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
        low: "bg-success", medium: "bg-info", high: "bg-warning", urgent: "bg-danger",
    };
    return map[priority.toLowerCase()] || "bg-light";
}

function renderAttachments(attachments) {
    try {
        if (!attachments || attachments.length === 0) return "<em>Tidak ada lampiran.</em>";
        return attachments.map(file => {
            const fileUrl = `/storage/${file}`;
            const fileName = file.split("/").pop();
            const isImage = /\.(jpg|jpeg|png|gif|webp)$/i.test(fileName);
            return isImage
                ? `<a href="${fileUrl}" target="_blank"><img src="${fileUrl}" alt="Attachment" class="img-thumbnail" style="max-height:100px;"></a>`
                : `<a href="${fileUrl}" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="fas fa-paperclip me-1"></i> ${fileName}</a>`;
        }).join("");
    } catch (err) {
        console.error("Attachment parse error:", err);
        return "Error loading attachments.";
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

            // ✅ Tambahkan SweetAlert sukses di sini
            Swal.fire({
                title: "Tiket Berhasil Dibuat!",
                text: "Tiket kamu sudah tersimpan dan sedang diproses.",
                icon: "success",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "Oke"
            });

            // Tutup modal kalau ada
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
            submitBtn.innerHTML = `<i class="fas fa-paper-plane me-1"></i> Submit Ticket`;
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
                            <td><a href="${BASE_URL}" class="fw-semibold">${ticket.ticket_id || '-'}</a></td>
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
document.addEventListener("DOMContentLoaded", () => {
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
});
// ==========================
// DATE FORMATTER - ADD THIS MISSING FUNCTION
// ==========================
function formatDateFromRaw(dateString) {
    if (!dateString || dateString === 'N/A') return 'N/A';
    
    console.log('🔧 Formatting date:', dateString);
    
    // Handle format "28-10-2025 10:08" (DD-MM-YYYY HH:MM)
    if (dateString.includes('-') && dateString.includes(' ')) {
        const [datePart, timePart] = dateString.split(' ');
        const [day, month, year] = datePart.split('-');
        
        if (day && month && year && timePart) {
            // Reformat to "2025-10-28 10:08"
            const formatted = `${year}-${month}-${day} ${timePart}`;
            console.log('✅ Formatted date:', formatted);
            return formatted;
        }
    }
    
    // Return original if not matching expected format
    console.log('ℹ️ Unknown format, returning original');
    return dateString;
}
// ==========================
// TICKET DETAIL MODAL HANDLER
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
    const modal = new bootstrap.Modal(document.getElementById('detailTicketModal'));
    const loader = document.getElementById('d_loader');
    const content = document.getElementById('d_content');
    
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
        
        // DEBUG: Lihat data di console
        console.log('Ticket data:', ticket);
        console.log('Created at raw:', ticket.created_at);
        console.log('Created at formatted:', ticket.created_at_formatted);
        
        // Populate modal dengan data
        document.getElementById('d_ticket_id').textContent = ticket.ticket_id || 'N/A';
        document.getElementById('d_user').textContent = ticket.user?.name || 'N/A';
        document.getElementById('d_department').textContent = ticket.department?.name || 'N/A';
        document.getElementById('d_category').textContent = ticket.category?.name || 'N/A';
        document.getElementById('d_status').textContent = ticket.status ? ticket.status.replace('_', ' ').toUpperCase() : 'N/A';
        document.getElementById('d_priority').textContent = ticket.priority ? ticket.priority.toUpperCase() : 'N/A';
        document.getElementById('d_description').textContent = ticket.description || 'No description';
        
        // FIX: Handle date dengan multiple fallback
        let displayDate = 'Date not available';
        
        if (ticket.created_at_formatted && ticket.created_at_formatted !== 'N/A') {
            // Jika sudah ada format, gunakan langsung
            displayDate = ticket.created_at_formatted;
        } else if (ticket.created_at) {
            // Format dari created_at raw
            displayDate = formatDateFromRaw(ticket.created_at);
        }
        
        console.log('Final display date:', displayDate);
        document.getElementById('d_created').textContent = displayDate;
        
        // Handle resolution notes
        const notesRow = document.getElementById('d_row_notes');
        const notesElement = document.getElementById('d_notes');
        if (ticket.resolution_notes) {
            notesElement.textContent = ticket.resolution_notes;
            notesRow.classList.remove('d-none');
        } else {
            notesRow.classList.add('d-none');
        }
        
        // Handle attachments
        const attachmentsContainer = document.getElementById('d_attachments');
        if (ticket.attachments && ticket.attachments.length > 0) {
            attachmentsContainer.innerHTML = renderAttachments(ticket.attachments);
        } else {
            attachmentsContainer.innerHTML = '<em>No attachments</em>';
        }
        
        // Update badge classes
        const statusBadge = document.getElementById('d_status');
        const priorityBadge = document.getElementById('d_priority');
        
        statusBadge.className = `badge ${getStatusBadgeClass(ticket.status)}`;
        priorityBadge.className = `badge ${getPriorityBadgeClass(ticket.priority)}`;
        
        // Hide loader, show content
        loader.classList.add('d-none');
        content.classList.remove('d-none');
        
        // Show modal
        modal.show();
        
    } catch (error) {
        console.error('Error loading ticket details:', error);
        loader.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Failed to load ticket details: ${error.message}
            </div>
        `;
    }
}
    // --- Handler untuk Update Status/Priority ---
    document.body.addEventListener('change', async (e) => {
        if (!e.target.classList.contains('update-ticket-field')) return;

        const select = e.target;
        const id = select.dataset.id;
        const field = select.dataset.field;
        const value = select.value;
        const old = select.dataset.originalValue;

        if (!confirm(`Ubah ${field} menjadi "${value}"?`)) {
            select.value = old;
            return;
        }

        if (field === 'status' && ['pending', 'closed'].includes(value)) {
            const modalEl = document.getElementById('resolutionModal');
            const modal = new bootstrap.Modal(modalEl);
            const notesTextarea = document.getElementById('resolutionNotes');
            notesTextarea.value = ''; // Clear previous notes

            const saveBtn = document.getElementById('saveResolutionBtn');
            const newSaveBtn = saveBtn.cloneNode(true);
            saveBtn.parentNode.replaceChild(newSaveBtn, saveBtn);

            newSaveBtn.addEventListener('click', async () => {
                const notes = notesTextarea.value.trim();
                await updateFieldWithNotes(select, id, field, value, old, notes);
                modal.hide();
            });
            modal.show();
        } else {
            await updateFieldWithNotes(select, id, field, value, old, null);
        }
    });

    async function updateFieldWithNotes(select, id, field, value, old, notes) {
        try {
            const res = await fetch(`${BASE_URL}/${id}/update-field`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ field, value, resolution_notes: notes })
            });

            const data = await res.json();
            if (!res.ok) {
                let errorMsg = data.message || `Gagal memperbarui. Status: ${res.status}`;
                if (data.errors) {
                    errorMsg += '\n' + Object.values(data.errors).map(e => e.join(', ')).join('\n');
                }
                throw new Error(errorMsg);
            }
            
            alert('Berhasil memperbarui tiket.');
            select.dataset.originalValue = value;
            if (field === 'status') {
                updateSelectColor(select, value);
            }
        } catch (err) {
            console.error(err);
            alert(err.message);
            select.value = old;
        }
    }
});