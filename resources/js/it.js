document.addEventListener("DOMContentLoaded", function () {
    // === Toast setup ===
    const toastEl = document.getElementById("liveToast");
    const toastBody = document.getElementById("toast-body-message");
    const toastIcon = toastEl.querySelector(".toast-header i");
    const bsToast = new bootstrap.Toast(toastEl);

    window.showToast = function(message, success=true){
        toastBody.textContent = message;
        toastIcon.className = success
            ? "fas fa-check-circle text-success me-2"
            : "fas fa-times-circle text-danger me-2";
        bsToast.show();
        toastEl.scrollIntoView({behavior: "smooth", block:"end"});
    }

    // === Confirm modal ===
    const confirmModalEl = document.getElementById("confirmUpdateModal");
    const confirmMsg = document.getElementById("confirmUpdateMessage");
    const confirmYesBtn = document.getElementById("confirmUpdateYes");
    const confirmNoBtn = document.getElementById("confirmUpdateNo");
    const bsConfirmModal = new bootstrap.Modal(confirmModalEl);
    let confirmCallback = null;

    window.showConfirm = function(message, onYes){
        confirmMsg.textContent = message;
        confirmCallback = onYes || null;
        bsConfirmModal.show();
    }

    confirmYesBtn.addEventListener("click", function(){
        if(confirmCallback) confirmCallback();
        confirmCallback = null;
        bsConfirmModal.hide();
    });
    confirmNoBtn.addEventListener("click", ()=>{confirmCallback=null});

    let pendingUpdate = null;

    // === Update status / priority ===
    document.body.addEventListener("change", function(e){
        if(e.target.classList.contains("update-ticket-field")){
            const select = e.target;
            const ticketId = select.dataset.id;
            const field = select.dataset.field;
            const value = select.value;
            const originalValue = [...select.options].find(opt=>opt.defaultSelected).value;

            pendingUpdate = {select,ticketId,field,value,originalValue};
            select.value = originalValue;

            showConfirm(`Are you sure you want to change ${field} of ticket #${ticketId} to "${value}"?`, function(){
                select.disabled = true;
                fetch(`/it/tickets/${ticketId}/update-field`,{
                    method: "POST",
                    headers:{
                        "Content-Type":"application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({field,value})
                })
                .then(res=>res.json().then(data=>({ok: res.ok,data})))
                .then(({ok,data})=>{
                    if(ok && data.success){
                        showToast(data.message || 'Ticket updated successfully.');

                        const row = document.querySelector(`tr[data-id="${ticketId}"]`);
                        if(row){
                            // Highlight baris
                            row.classList.add('table-success');
                            setTimeout(()=>row.classList.remove('table-success'),1500);

                            // Jika status menjadi closed/resolved -> pindah ke riwayat
                            if(field === 'status' && ['closed','resolved'].includes(value)){
                                fetch(`/it/tickets/${ticketId}`)
                                .then(res=>res.json())
                                .then(ticket=>{
                                    row.remove(); // hapus dari ticket list

                                    const historyBody = document.getElementById('historyTicketsBody');
                                    if(historyBody){
                                        const tr = document.createElement('tr');
                                        tr.dataset.id = ticket.id;
                                        tr.innerHTML = `
                                            <td>${ticket.ticket_id}</td>
                                            <td>${ticket.description}</td>
                                            <td>${ticket.category?.name ?? '-'}</td>
                                            <td>${ticket.status}</td>
                                            <td>${ticket.priority}</td>
                                            <td>${ticket.user?.name ?? 'Unknown'}</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary btn-detail-ticket" data-id="${ticket.id}" data-bs-toggle="modal" data-bs-target="#ticketDetailModal">
                                                    <i class="fas fa-eye me-1"></i> Detail
                                                </button>
                                            </td>`;
                                        historyBody.prepend(tr);
                                    }
                                });
                            }
                        }

                        // update select default
                        [...select.options].forEach(opt=>opt.defaultSelected = (opt.value==value));
                        select.value = value;
                    } else {
                        showToast(data.message || 'Failed to update ticket', false);
                        select.value = originalValue;
                    }
                })
                .catch(err=>{ showToast('Server error', false); select.value = originalValue; })
                .finally(()=>select.disabled=false);
            });
        }
    });

    // === Detail ticket modal ===
    document.body.addEventListener("click", function(e){
        if(e.target.closest(".btn-detail-ticket")){
            const btn = e.target.closest(".btn-detail-ticket");
            const ticketId = btn.dataset.id;
            fetch(`/it/tickets/${ticketId}`)
            .then(res=>res.json())
            .then(ticket=>{
                document.getElementById('modalTicketId').textContent = ticket.ticket_id;
                document.getElementById('modalTicketDescription').textContent = ticket.description;
                document.getElementById('modalTicketStatus').textContent = ticket.status;
                document.getElementById('modalTicketPriority').textContent = ticket.priority;
                document.getElementById('modalTicketCategory').textContent = ticket.category?.name ?? '-';
            })
            .catch(()=>showToast('Failed to load ticket detail', false));
        }
    });
});
