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
    // LOGIKA UNTUK UPLOAD FILE (SUDAH BENAR, TIDAK DIUBAH)
    // =========================================================================
    if (uploadArea && fileInput && uploadedFilesDiv && filesList) {
        // ... (Semua kode upload file Anda tetap sama) ...
        uploadArea.addEventListener('dragover', (e) => { e.preventDefault(); uploadArea.classList.add('dragover'); });
        uploadArea.addEventListener('dragleave', () => { uploadArea.classList.remove('dragover'); });
        uploadArea.addEventListener('drop', (e) => { e.preventDefault(); uploadArea.classList.remove('dragover'); const files = Array.from(e.dataTransfer.files); handleFiles(files); });
        fileInput.addEventListener('change', (e) => { const files = Array.from(e.target.files); handleFiles(files); });
        function handleFiles(newFiles) { let validFiles = newFiles.filter(file => validateFile(file)); uploadedFiles.push(...validFiles); updateFilesList(); fileInput.value = ''; }
        function validateFile(file) { const maxSize = 5 * 1024 * 1024; const allowedTypes = ['image/jpeg', 'image/png', 'image/heif']; if (file.size > maxSize) { alert('File is too large. Maximum size is 5MB per file.'); return false; } if (!allowedTypes.includes(file.type)) { alert('File type not supported. Only jpg, png, are allowed.'); return false; } return true; }
        function updateFilesList() { filesList.innerHTML = ''; if (uploadedFiles.length > 0) { uploadedFilesDiv.style.display = 'block'; uploadArea.style.display = 'block'; uploadedFiles.forEach((file, index) => { const fileItem = document.createElement('div'); fileItem.className = 'file-item d-flex justify-content-between align-items-center p-2 border rounded-3 bg-light mb-2'; fileItem.innerHTML = `<div><i class="fas fa-file-alt me-2 text-info"></i><span>${file.name}</span><small class="text-muted ms-2">(${formatFileSize(file.size)})</small></div><button type="button" class="btn-close" aria-label="Remove" data-index="${index}"></button>`; filesList.appendChild(fileItem); }); } else { uploadedFilesDiv.style.display = 'none'; } }
        filesList.addEventListener('click', function(event) { if (event.target.classList.contains('btn-close')) { const indexToRemove = parseInt(event.target.dataset.index, 10); removeFile(indexToRemove); } });
        function removeFile(index) { uploadedFiles.splice(index, 1); updateFilesList(); }
        function formatFileSize(bytes) { if (bytes === 0) return '0 Bytes'; const k = 1024; const sizes = ['Bytes', 'KB', 'MB']; const i = Math.floor(Math.log(bytes) / Math.log(k)); return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]; }
    }

    // =========================================================================
    // FUNGSI UNTUK SUBMIT TICKET (SUDAH BENAR, TIDAK DIUBAH)
    // =========================================================================
    async function submitTicket(event) {
        // ... (Semua kode submit ticket Anda tetap sama) ...
        event.preventDefault(); const form = event.target; const submitButton = form.querySelector('button[type="submit"]'); if (!form.checkValidity()) { form.reportValidity(); return; } const formData = new FormData(form); if (uploadedFiles.length > 0) { uploadedFiles.forEach(file => { formData.append('attachments[]', file); }); } submitButton.disabled = true; submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...`; try { const response = await fetch(form.action, { method: 'POST', body: formData, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', }, }); const result = await response.json(); if (response.ok) { alert(result.message); const modal = bootstrap.Modal.getInstance(document.getElementById('createTicketModal')); if (modal) modal.hide(); setTimeout(() => { form.reset(); uploadedFiles = []; updateFilesList(); window.location.reload(); }, 500); } else { let errorMessages = Object.values(result.errors).map(error => `- ${error[0]}`).join('\n'); alert('Failed to create ticket:\n' + errorMessages); } } catch (error) { console.error('Error:', error); alert('A connection error occurred. Please try again.'); } finally { submitButton.disabled = false; submitButton.innerHTML = `<i class="fas fa-paper-plane me-1"></i> Submit Ticket`; }
    }

    // Pasang event listener ke form tiket jika ada
    if (ticketForm) {
        ticketForm.addEventListener('submit', submitTicket);
    }

    // 🔽🔽🔽 --- PERBAIKAN DIMULAI DARI SINI --- 🔽🔽🔽

    // 1. Tambahkan event listener untuk tombol sidebar toggler
    const sidebarToggler = document.getElementById('sidebarToggler');
    if (sidebarToggler) {
        sidebarToggler.addEventListener('click', toggleSidebar);
    }

    // Tambahkan juga event listener untuk overlay agar bisa menutup sidebar
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }
    
    // 🔼🔼🔼 --- PERBAIKAN SELESAI DI SINI --- 🔼🔼🔼
});


// =========================================================================
// GLOBAL FUNCTIONS (FUNGSI SIDEBAR DIPERBAIKI)
// =========================================================================

// 2. Ubah 'active' menjadi 'show' agar sesuai dengan file CSS Anda
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("show");
    document.getElementById("sidebarOverlay").classList.toggle("show");
}

function closeSidebar() {
    document.getElementById("sidebar").classList.remove("show");
    document.getElementById("sidebarOverlay").classList.remove("show");
}