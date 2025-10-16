document.addEventListener("DOMContentLoaded", () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || "";
    const defaultHeaders = {
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-TOKEN": csrfToken,
        "Accept": "application/json",
    };

    // Event: klik tombol detail tiket
    document.addEventListener("click", async (e) => {
        const btn = e.target.closest(".btn-detail-ticket");
        if (!btn) return;

        const id = btn.dataset.id;
        if (!id) return;

        // Tampilkan loader dulu
        document.getElementById("d_content").classList.add("d-none");
        document.getElementById("d_loader").classList.remove("d-none");

        const modalEl = document.getElementById("detailTicketModal");
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        try {
            const res = await fetch(`/staff/tickets/${id}`, { headers: defaultHeaders });
            if (!res.ok) throw new Error(`Gagal memuat tiket (${res.status})`);
            const ticket = await res.json();

            // Isi data ke modal
            document.getElementById("d_ticket_id").textContent = ticket.ticket_id || "-";
            document.getElementById("d_department").textContent = ticket.user?.department?.name || "-";
            document.getElementById("d_category").textContent = ticket.category?.name || "-";
            document.getElementById("d_description").textContent = ticket.description || "-";
            document.getElementById("d_user").textContent = ticket.user?.name || "-";
            document.getElementById("d_created").textContent = ticket.created_at || "-";
            document.getElementById("d_status").textContent = ticket.status || "-";
            document.getElementById("d_priority").textContent = ticket.priority || "-";

            // Tampilkan isi, sembunyikan loader
            document.getElementById("d_loader").classList.add("d-none");
            document.getElementById("d_content").classList.remove("d-none");
        } catch (err) {
            console.error("Error detail:", err);
            alert("Gagal memuat detail tiket.");
            modal.hide();
        }
    });
});
