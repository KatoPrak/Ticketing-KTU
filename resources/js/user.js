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
    // LOGIKA UNTUK UPLOAD FILE (DRAG & DROP, KLIK, VALIDASI)
    // =========================================================================

    // Cek null untuk memastikan elemen ada sebelum menambah event listener
    if (uploadArea && fileInput && uploadedFilesDiv && filesList) {
        
        // --- Event Listeners untuk Upload Area ---
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

        // --- Fungsi Utama untuk Menangani File ---
        function handleFiles(files) {
            const file = files[0]; // Hanya proses satu file
            uploadedFiles = [];   // Kosongkan array sebelum menambah file baru

            if (file && validateFile(file)) {
                uploadedFiles.push(file);
            }
            updateFilesList();
            fileInput.value = ''; // Reset input agar bisa upload file yang sama lagi
        }

        // --- Fungsi Validasi File ---
        function validateFile(file) {
            const maxSize = 5 * 1024 * 1024; // 5MB
            const allowedTypes = [
                'image/jpeg',
                'image/png',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            
            if (file.size > maxSize) {
                alert('File terlalu besar. Maksimal 5MB per file.');
                return false;
            }
            
            if (!allowedTypes.includes(file.type)) {
                alert('Tipe file tidak didukung. Hanya jpg, png, pdf, doc, docx yang diperbolehkan.');
                return false;
            }
            
            return true;
        }

        // --- Fungsi Perbarui Tampilan Daftar File ---
        function updateFilesList() {
            filesList.innerHTML = '';
            if (uploadedFiles.length > 0) {
                uploadedFilesDiv.style.display = 'block';
                uploadArea.style.display = 'none';

                uploadedFiles.forEach((file, index) => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'file-item d-flex justify-content-between align-items-center p-2 border rounded-3 bg-light';
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
                uploadArea.style.display = 'block';
            }
        }

        // --- Fungsi Hapus File ---
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

        // --- Fungsi Pembantu untuk Format Ukuran File ---
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    }


    // =========================================================================
    // FUNGSI UNTUK SUBMIT TICKET KE SERVER LARAVEL
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
            formData.append('attachment', uploadedFiles[0]);
        }

        submitButton.disabled = true;
        submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...`;

        try {
            const response = await fetch(form.action, { // Ambil URL dari atribut action form
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const result = await response.json();

            if (response.ok) {
                alert(result.message);
                const modal = bootstrap.Modal.getInstance(document.getElementById('createTicketModal'));
                if (modal) modal.hide();
                
                setTimeout(() => {
                    form.reset();
                    uploadedFiles = [];
                    updateFilesList();
                    window.location.reload(); // Reload halaman untuk melihat data baru
                }, 500);

            } else {
                let errorMessages = Object.values(result.errors).map(error => `- ${error[0]}`).join('\n');
                alert('Gagal membuat tiket:\n' + errorMessages);
            }

        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan koneksi. Silakan coba lagi.');
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = `<i class="fas fa-paper-plane me-1"></i> Kirim Tiket`;
        }
    }

    // Pasang event listener ke form jika ada
    if (ticketForm) {
        ticketForm.addEventListener('submit', submitTicket);
    }
});


// =========================================================================
// FUNGSI-FUNGSI GLOBAL (MISALNYA UNTUK SIDEBAR)
// =========================================================================
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("active");
    document.getElementById("sidebarOverlay").classList.toggle("active");
}

function closeSidebar() {
    document.getElementById("sidebar").classList.remove("active");
    document.getElementById("sidebarOverlay").classList.remove("active");
}