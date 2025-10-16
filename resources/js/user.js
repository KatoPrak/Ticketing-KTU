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
    // LOGIKA UPLOAD FILE
    // =========================================================================
    if (uploadArea && fileInput && uploadedFilesDiv && filesList) {
        uploadArea.addEventListener('dragover', e => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });
        uploadArea.addEventListener('drop', e => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            handleFiles(Array.from(e.dataTransfer.files));
        });
        fileInput.addEventListener('change', e => {
            handleFiles(Array.from(e.target.files));
        });

        function handleFiles(newFiles) {
            const validFiles = newFiles.filter(validateFile);
            uploadedFiles.push(...validFiles);
            updateFilesList();
            fileInput.value = '';
        }

        function validateFile(file) {
            const maxSize = 5 * 1024 * 1024;
            const allowedTypes = ['image/jpeg', 'image/png', 'image/heif'];
            if (file.size > maxSize) {
                alert('File terlalu besar (maksimum 5MB).');
                return false;
            }
            if (!allowedTypes.includes(file.type)) {
                alert('Jenis file tidak didukung. Hanya JPG dan PNG.');
                return false;
            }
            return true;
        }

        function updateFilesList() {
            filesList.innerHTML = '';
            if (uploadedFiles.length > 0) {
                uploadedFilesDiv.style.display = 'block';
                uploadedFiles.forEach((file, index) => {
                    const item = document.createElement('div');
                    item.className = 'file-item d-flex justify-content-between align-items-center p-2 border rounded-3 bg-light mb-2';
                    item.innerHTML = `
                        <div>
                            <i class="fas fa-file-alt me-2 text-info"></i>
                            <span>${file.name}</span>
                            <small class="text-muted ms-2">(${formatFileSize(file.size)})</small>
                        </div>
                        <button type="button" class="btn-close" aria-label="Remove" data-index="${index}"></button>
                    `;
                    filesList.appendChild(item);
                });
            } else {
                uploadedFilesDiv.style.display = 'none';
            }
        }

        filesList.addEventListener('click', e => {
            if (e.target.classList.contains('btn-close')) {
                const idx = parseInt(e.target.dataset.index, 10);
                uploadedFiles.splice(idx, 1);
                updateFilesList();
            }
        });

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
        uploadedFiles.forEach(f => formData.append('attachments[]', f));

        submitButton.disabled = true;
        submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status"></span> Submitting...`;

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
                const modal = bootstrap.Modal.getInstance(document.getElementById('createTicketModal'));
                if (modal) modal.hide();

                form.reset();
                uploadedFiles = [];
                updateFilesList();

                Swal.fire({
                    icon: 'success',
                    title: 'Ticket created!',
                    text: result.message || 'Your ticket has been added successfully.',
                    timer: 1500,
                    showConfirmButton: false
                });

                refreshTicketsList();
            } else {
                const errors = Object.values(result.errors)
                    .map(e => `- ${e[0]}`).join('\n');
                alert('Gagal membuat tiket:\n' + errors);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan koneksi.');
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = `<i class="fas fa-paper-plane me-1"></i> Submit Ticket`;
        }
    }

    if (ticketForm) ticketForm.addEventListener('submit', submitTicket);

    // =========================================================================
    // SIDEBAR HANDLER
    // =========================================================================
    const sidebarToggler = document.getElementById('sidebarToggler');
    if (sidebarToggler) sidebarToggler.addEventListener('click', toggleSidebar);

    const sidebarOverlay = document.getElementById('sidebarOverlay');
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);
});

// =========================================================================
// REFRESH LIST TIKET TANPA RELOAD
// =========================================================================
function refreshTicketsList() {
    fetch('/tickets', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector('#ticketsTable tbody');
            tbody.innerHTML = '';

            data.tickets.forEach(ticket => {
                const dept = ticket.user?.department?.name ?? '-';
                const category = ticket.category?.name ?? '-';
                const user = ticket.user?.name ?? '-';

                const row = `
                    <tr>
                        <td>${ticket.ticket_id}</td>
                        <td>${user}</td>
                        <td>${dept}</td>
                        <td>${category}</td>
                        <td>${ticket.priority}</td>
                        <td>${ticket.status}</td>
                        <td>${ticket.created_at_formatted}</td>
                       <td>
  <button class="btn btn-info btn-sm btn-detail-ticket" data-id="${ticket.id}">
    Detail
  </button>
</td>

                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        })
        .catch(err => console.error('Refresh error:', err));
}


// =========================================================================
// SIDEBAR
// =========================================================================
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("show");
    document.getElementById("sidebarOverlay").classList.toggle("show");
}

function closeSidebar() {
    document.getElementById("sidebar").classList.remove("show");
    document.getElementById("sidebarOverlay").classList.remove("show");
}
