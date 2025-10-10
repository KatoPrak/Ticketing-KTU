<div class="modal fade" id="createTicketModal" tabindex="-1" aria-labelledby="createTicketModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered custom-modal">
        <div class="modal-content shadow-lg border-0">
            <form class="ticketForm" action="{{ route('staff.tickets.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0">
                    <h1 class="modal-title fs-4 fw-bold text-white" id="createTicketModalLabel">
                        <i class="fas fa-ticket-alt me-2"></i> Create New Ticket
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control"
                                value="{{ Auth::user()->department ?? 'Not set' }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Email</label>
                            <input type="email" class="form-control" value="{{ Auth::user()->email }}" disabled>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="description" style="height: 120px" required></textarea>
                        </div>
                        <input type="hidden" name="priority" value="low">
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-paperclip me-1 text-secondary"></i> File Attachments
                                <small class="text-muted">(optional, .jpg .png .jpeg .heif)</small>
                            </label>
                            <input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.heif"
                                class="form-control">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> Submit Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".ticketForm").forEach(form => {
            form.addEventListener("submit", function (e) {
                e.preventDefault();
                let formData = new FormData(form);

                fetch(form.action, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const ticket = data.ticket;
                        const priority = (ticket.priority || 'low').toString().toLowerCase();
                        const priorityLabel = priority.charAt(0).toUpperCase() + priority.slice(1);
                        const priorityClass = priority === 'high' ? 'bg-danger'
                            : priority === 'medium' ? 'bg-warning text-dark'
                            : 'bg-success';

                        const description = ticket.description
                            ? (ticket.description.length > 50 ? ticket.description.substring(0,50) + '...' : ticket.description)
                            : '-';

                        const attachmentsStr = JSON.stringify(ticket.attachments || []).replace(/'/g, "&#39;");
                        const categoryName = ticket.category && ticket.category.name ? ticket.category.name : '-';
                        const ticketsTable = document.querySelector("#ticketsTable tbody");

                        if (ticketsTable) {
                            const newRow = `
                                <tr>
                                    <td>${ticket.ticket_id ?? ''}</td>
                                    <td>${categoryName}</td>
                                    <td>${description}</td>
                                    <td><span class="badge bg-secondary">${ticket.status ?? '-'}</span></td>
                                    <td><span class="badge ${priorityClass}">${priorityLabel}</span></td>
                                    <td>${ticket.created_at ?? ''}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-ticket-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#showTicketModal"
                                            data-id="${ticket.ticket_id ?? ''}"
                                            data-category="${categoryName}"
                                            data-description="${ticket.description ?? ''}"
                                            data-status="${ticket.status ?? ''}"
                                            data-priority="${priority}"
                                            data-created="${ticket.created_at ?? ''}"
                                            data-attachments='${attachmentsStr}'>
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                            ticketsTable.insertAdjacentHTML("afterbegin", newRow);
                        }

                        form.reset();
                        const modalEl = form.closest(".modal");
                        if (modalEl) {
                            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                            modal.hide();
                        }
                        alert(data.message); // This message comes from the server
                        setTimeout(() => {
                            location.reload();
                        }, 500);

                        if (typeof bindViewButtonEvents === 'function') bindViewButtonEvents();
                    } else {
                        alert("Failed to create ticket!");
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("An error occurred while sending the ticket.");
                });
            });
        });
    });
</script>