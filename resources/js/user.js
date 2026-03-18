document.addEventListener('DOMContentLoaded', function () {
    // =========================================================================
    // VARIABEL GLOBAL UNTUK FILE UPLOAD
    // =========================================================================
    let uploadedFiles = [];
    const fileInput = document.getElementById('fileInput');
    const uploadArea = document.querySelector('.file-upload-area');
    const uploadedFilesDiv = document.getElementById('uploadedFiles');
    const filesList = document.getElementById('filesList');

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
            const allowedTypes = ['image/jpeg', 'image/png', 'image/heif', 'image/jpg'];

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
                    item.className = 'file-item d-flex justify-content-between align-items-center p-2 border rounded-3 bg-light mb-2 shadow-sm';

                    // Cek apakah file adalah gambar untuk menampilkan preview (thumbnail)
                    const isImage = file.type.startsWith('image/');
                    let filePreview = '';

                    if (isImage) {
                        // Gunakan URL sementara untuk menampilkan pratinjau lokal
                        const objectUrl = URL.createObjectURL(file);
                        filePreview = `<img src="${objectUrl}" alt="preview" class="rounded border me-3" style="width: 50px; height: 50px; object-fit: cover;">`;
                    } else {
                        filePreview = `<i class="fas fa-file-alt fa-2x text-info me-3"></i>`;
                    }

                    item.innerHTML = `
                        <div class="d-flex align-items-center w-100 overflow-hidden">
                            ${filePreview}
                            <div class="text-truncate">
                                <strong class="d-block text-truncate" style="max-width: 200px; font-size: 0.9rem;">${file.name}</strong>
                                <small class="text-muted">${formatFileSize(file.size)}</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close ms-2" aria-label="Remove" data-index="${index}"></button>
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
// Add this to your existing JavaScript
function adaptTableForMobile() {
    const table = document.querySelector('.table');
    if (!table) return;

    const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
    const rows = table.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        cells.forEach((cell, index) => {
            if (headers[index]) {
                cell.setAttribute('data-label', headers[index]);
            }
        });
    });
}
// Panggil fungsi saat load dan resize
document.addEventListener('DOMContentLoaded', function () {
    adaptTableForMobile();
    window.addEventListener('resize', adaptTableForMobile);
});