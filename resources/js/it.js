// ==========================
// it.js — versi final clean & global safe
// ==========================

import Swal from "sweetalert2";

// Base URL untuk IT route
const BASE_URL = "/it/tickets";

// ==========================
// Helper: render lampiran
// ==========================
function renderAttachments(attachments) {
    try {
        const files = JSON.parse(attachments);
        if (!files.length) return "-";

        return files
            .map((file) => {
                const ext = file.split(".").pop().toLowerCase();
                const isImage = ["jpg", "jpeg", "png", "gif", "webp"].includes(ext);
                const fileUrl = `/storage/${file}`;

                if (isImage) {
                    return `
                        <div class="mb-2">
                            <a href="${fileUrl}" target="_blank">
                                <img src="${fileUrl}" alt="Attachment" class="img-thumbnail" style="max-height:150px;">
                            </a>
                        </div>
                    `;
                } else {
                    return `
                        <div class="mb-2">
                            <a href="${fileUrl}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-paperclip me-1"></i> ${file.split("/").pop()}
                            </a>
                        </div>
                    `;
                }
            })
            .join("");
    } catch (err) {
        console.error("Attachment parse error:", err);
        return "-";
    }
}
window.renderAttachments = renderAttachments;

// ==========================
// Helper: tampilkan alert/toast
// ==========================
function showToast(icon, title) {
    Swal.fire({
        toast: true,
        icon: icon,
        title: title,
        position: "top-end",
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
    });
}
window.showToast = showToast;

// ==========================
// Detail Ticket Modal Logic
// ==========================
document.addEventListener("click", async (e) => {
    const btn = e.target.closest(".btn-detail-ticket");
    if (!btn) return;

    const ticketId = btn.dataset.id;
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

        // Isi data ke modal
        document.getElementById("d_ticket_id").innerText = ticket.ticket_id || "-";
        document.getElementById("d_description").innerText = ticket.description || "-";
        document.getElementById("d_category").innerText = ticket.category?.name || "-";
        document.getElementById("d_priority").innerText = ticket.priority || "-";
        document.getElementById("d_status").innerText = ticket.status || "-";
        document.getElementById("d_user").innerText = ticket.user?.name || "-";
        document.getElementById("d_department").innerText = ticket.user?.department || "-";
        document.getElementById("d_created").innerText = ticket.created_at || "-";

        const attachContainer = document.getElementById("d_attachments");
        attachContainer.innerHTML = renderAttachments(ticket.attachments);
    } catch (err) {
        console.error("Gagal load detail tiket:", err);
        showToast("error", "Gagal memuat detail tiket");
    } finally {
        loader.classList.add("d-none");
        content.classList.remove("d-none");
    }
});

// ==========================
// Hapus Ticket via AJAX
// ==========================
document.addEventListener("click", async (e) => {
    const btn = e.target.closest(".btn-delete-ticket");
    if (!btn) return;

    const ticketId = btn.dataset.id;
    if (!ticketId) return;

    const confirm = await Swal.fire({
        title: "Hapus tiket ini?",
        text: "Data tidak dapat dikembalikan setelah dihapus!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Ya, hapus",
        cancelButtonText: "Batal",
    });

    if (!confirm.isConfirmed) return;

    try {
        const res = await fetch(`${BASE_URL}/${ticketId}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
        });

        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        showToast("success", "Tiket berhasil dihapus");
        document.getElementById(`ticket-row-${ticketId}`)?.remove();
    } catch (err) {
        console.error("Gagal hapus tiket:", err);
        showToast("error", "Gagal menghapus tiket");
    }
});

// ==========================
// Sidebar Toggle & Overlay
// ==========================
function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("sidebarOverlay");

    if (!sidebar || !overlay) {
        console.warn("Sidebar atau overlay tidak ditemukan di DOM.");
        return;
    }

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

// Tutup sidebar saat resize ke desktop
window.addEventListener("resize", () => {
    if (window.innerWidth >= 992) {
        closeSidebar();
    }
});

// Tutup sidebar saat tekan ESC
document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeSidebar();
});

// Tutup sidebar saat klik overlay
document.addEventListener("click", (e) => {
    if (e.target.id === "sidebarOverlay") closeSidebar();
});

// ==========================
// Modal: Change Password (Global Trigger)
// ==========================
function openChangePasswordModal() {
    const modalEl = document.getElementById("changePasswordModal");
    if (!modalEl) return showToast("error", "Modal tidak ditemukan!");
    new bootstrap.Modal(modalEl).show();
}
window.openChangePasswordModal = openChangePasswordModal;

// Simpan perubahan password
async function submitChangePassword(e) {
    e.preventDefault();
    const form = e.target;
    const data = new FormData(form);

    try {
        const res = await fetch("/user/change-password", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content },
            body: data,
        });

        const result = await res.json();
        if (res.ok) {
            showToast("success", result.message || "Password berhasil diubah");
            bootstrap.Modal.getInstance(document.getElementById("changePasswordModal")).hide();
            form.reset();
        } else {
            showToast("error", result.message || "Gagal mengubah password");
        }
    } catch (err) {
        console.error("Error:", err);
        showToast("error", "Terjadi kesalahan saat ubah password");
    }
}
window.submitChangePassword = submitChangePassword;

// ==========================
// (Opsional) Auto Refresh Ticket List
// ==========================
// setInterval(refreshTicketList, 60000);
