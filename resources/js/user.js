document.addEventListener('DOMContentLoaded', function() {
    // =========================================================================
    // VARIABEL GLOBAL UNTUK FILE UPLOAD
    // =========================================================================
    let uploadedFiles = [];
    const fileInput = document.getElementById('fileInput');
    const uploadArea = document.querySelector('.file-upload-area');
    const uploadedFilesDiv = document.getElementById('uploadedFiles');
    const filesList = document.getElementById('filesList');
    const ticketForm = document.getElementById('ticketForm');

    // =========================================================================
    // LOGIKA UNTUK UPLOAD FILE
    // =========================================================================
    if (uploadArea && fileInput && uploadedFilesDiv && filesList) {
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            const files = Array.from(e.dataTransfer.files);
            handleFiles(files);
        });
        fileInput.addEventListener('change', (e) => {
            const files = Array.from(e.target.files);
            handleFiles(files);
        });

        function handleFiles(newFiles) {
            let validFiles = newFiles.filter(file => validateFile(file));
            uploadedFiles.push(...validFiles);
            updateFilesList();
            fileInput.value = '';
        }

        function validateFile(file) {
            const maxSize = 5 * 1024 * 1024;
            const allowedTypes = ['image/jpeg', 'image/png', 'image/heif'];
            if (file.size > maxSize) {
                alert('File is too large. Maximum size is 5MB per file.');
                return false;
            }
            if (!allowedTypes.includes(file.type)) {
                alert('File type not supported. Only jpg, png are allowed.');
                return false;
            }
            return true;
        }

        function updateFilesList() {
            filesList.innerHTML = '';
            if (uploadedFiles.length > 0) {
                uploadedFilesDiv.style.display = 'block';
                uploadArea.style.display = 'block';
                uploadedFiles.forEach((file, index) => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'file-item d-flex justify-content-between align-items-center p-2 border rounded-3 bg-light mb-2';
                    fileItem.innerHTML = `
                        <div>
                            <i class="fas fa-file-alt me-2 text-info"></i>
                            <span>${file.name}</span>
                            <small class="text-muted ms-2">(${formatFileSize(file.size)})</small>
                        </div>
                        <button type="button" class="btn-close" aria-label="Remove" data-index="${index}"></button>
                    `;
                    filesList.appendChild(fileItem);
                });
            } else {
                uploadedFilesDiv.style.display = 'none';
            }
        }

        filesList.addEventListener('click', function(event) {
            if (event.target.classList.contains('btn-close')) {
                const indexToRemove = parseInt(event.target.dataset.index, 10);
                removeFile(indexToRemove);
            }
        });

        function removeFile(index) {
            uploadedFiles.splice(index, 1);
            updateFilesList();
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    }

    // =========================================================================
    // SUBMIT TICKET DENGAN AJAX TANPA RELOAD
    // =========================================================================
    async function submitTicket(event) {
        event.preventDefault();
        const form = event.target;
        const submitButton = form.querySelector('button[type="submit"]');

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);
        if (uploadedFiles.length > 0) {
            uploadedFiles.forEach(file => formData.append('attachments[]', file));
        }

        submitButton.disabled = true;
        submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...`;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const result = await response.json();

 if (response.ok) {
    const ticket = result.ticket;

    // Tutup modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('createTicketModal'));
    if (modal) modal.hide();

    // Reset form dan file list
    form.reset();
    uploadedFiles = [];
    updateFilesList();

    // SweetAlert notifikasi
    Swal.fire({
        icon: 'success',
        title: 'Ticket created!',
        text: result.message || 'Your ticket has been added successfully.',
        timer: 1500,
        showConfirmButton: false
    });

    // Tambah baris baru di tabel tanpa reload
    appendTicketRow(ticket);
    refreshTicketsList();

}
 else {
                let errorMessages = Object.values(result.errors)
                    .map(error => `- ${error[0]}`).join('\n');
                alert('Failed to create ticket:\n' + errorMessages);
            }

        } catch (error) {
            console.error('Error:', error);
            alert('A connection error occurred. Please try again.');
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = `<i class="fas fa-paper-plane me-1"></i> Submit Ticket`;
        }
    }

    if (ticketForm) {
        ticketForm.addEventListener('submit', submitTicket);
    }

    // =========================================================================
    // SIDEBAR HANDLER
    // =========================================================================
    const sidebarToggler = document.getElementById('sidebarToggler');
    if (sidebarToggler) sidebarToggler.addEventListener('click', toggleSidebar);

    const sidebarOverlay = document.getElementById('sidebarOverlay');
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);
});


// =========================================================================
// FUNGSI GLOBAL: REFRESH LIST TIKET TANPA RELOAD
// =========================================================================
function refreshTicketsList() {
    fetch(window.location.href, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector('#ticketTableBody');
            tbody.innerHTML = '';

            data.data.forEach(ticket => {
                const departmentName = ticket.user?.department?.name || '-';
                const categoryName = ticket.category?.name || '-';
                const shortDesc = ticket.description.length > 50
                    ? ticket.description.substring(0, 47) + '...'
                    : ticket.description;

                const statusClass = {
                    open: 'bg-success',
                    progress: 'bg-warning text-dark',
                    resolved: 'bg-primary',
                    closed: 'bg-danger',
                    waiting: 'bg-secondary'
                }[ticket.status.toLowerCase()] || 'bg-secondary';

                const priorityClass = {
                    high: 'bg-danger',
                    medium: 'bg-warning text-dark',
                    low: 'bg-success'
                }[ticket.priority.toLowerCase()] || 'bg-secondary';

                const row = document.createElement('tr');
                row.id = `ticket-row-${ticket.id}`;
                row.innerHTML = `
                    <td>${ticket.ticket_id}</td>
                    <td>${departmentName}</td>
                    <td>${categoryName}</td>
                    <td>${shortDesc}</td>
                    <td><span class="badge ${statusClass}">${ticket.status}</span></td>
                    <td><span class="badge ${priorityClass}">${ticket.priority}</span></td>
                    <td>${ticket.created_at_formatted ?? new Date(ticket.created_at).toLocaleString()}</td>
                    <td>
                        <button class="btn btn-sm btn-primary btn-detail-ticket" data-id="${ticket.id}">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });

            // re-bind tombol detail biar event-nya hidup lagi
            document.querySelectorAll('.btn-detail-ticket').forEach(btn => {
                btn.addEventListener('click', e => {
                    const id = e.currentTarget.dataset.id;
                    openTicketDetail(id);
                });
            });
        })
        .catch(err => console.error('Refresh tickets failed:', err));
}



// =========================================================================
// GLOBAL FUNCTIONS (SIDEBAR)
// =========================================================================
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("show");
    document.getElementById("sidebarOverlay").classList.toggle("show");
}

function closeSidebar() {
    document.getElementById("sidebar").classList.remove("show");
    document.getElementById("sidebarOverlay").classList.remove("show");
}
