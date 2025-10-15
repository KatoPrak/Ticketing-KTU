/* ================================
   🧱 CORE DASHBOARD
================================ */
function toggleSidebar() {
    document.getElementById('sidebar')?.classList.toggle('collapsed');
}

function showSection(sectionName, menuItem) {
    document.querySelectorAll('.content-section').forEach(section =>
        section.classList.remove('active')
    );
    document.getElementById(sectionName)?.classList.add('active');

    document.querySelectorAll('.menu-item').forEach(item =>
        item.classList.remove('active')
    );
    if (menuItem) menuItem.classList.add('active');
}

/* ================================
   👥 USER MODAL HANDLING
================================ */

// ✅ OPEN MODAL (ADD USER)
function openUserModal() {
    const modal = new bootstrap.Modal(document.getElementById('userModal'));
    const form = document.getElementById('userForm');
    const title = document.getElementById('modalTitle');

    // Reset form agar bersih
    form.reset();
    form.action = '/admin/users'; // Route store default
    title.innerHTML = '<i class="fas fa-user-plus me-2"></i> Add User';

    // Pastikan _method dihapus (jangan ada PUT saat tambah baru)
    const methodInput = form.querySelector('input[name="_method"]');
    if (methodInput) methodInput.remove();

    modal.show();
}

// ✅ CLOSE MODAL
function closeUserModal() {
    const modalEl = document.getElementById('userModal');
    const modal = bootstrap.Modal.getInstance(modalEl);
    const form = document.getElementById('userForm');
    if (form) form.reset();
    if (modal) modal.hide();
}

/* ================================
   ✏️ EDIT USER
================================ */
function editUser(id) {
    const modalEl = document.getElementById('userModal');
    const form = document.getElementById('userForm');
    const title = document.getElementById('modalTitle');
    const modal = new bootstrap.Modal(modalEl);

    title.innerHTML = '<i class="fas fa-user-edit me-2"></i> Edit User';
    form.action = `/admin/users/${id}`;

    // Tambahkan _method PUT untuk update
    let methodInput = form.querySelector('input[name="_method"]');
    if (!methodInput) {
        methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        form.appendChild(methodInput);
    }
    methodInput.value = 'PUT';

    // Tampilkan loading state ringan
    modal.show();
    form.classList.add('opacity-50');

    // Ambil data user
    fetch(`/admin/users/${id}`)
        .then(res => res.ok ? res.json() : Promise.reject('Failed to load user'))
        .then(user => {
            form.querySelector('#user_id').value = user.id || '';
            form.querySelector('#name').value = user.name || '';
            form.querySelector('#id_staff').value = user.id_staff || '';
            form.querySelector('#email').value = user.email || '';
            form.querySelector('#role').value = user.role || '';
            form.querySelector('#department_id').value = user.department_id || '';
            form.classList.remove('opacity-50');
        })
        .catch(err => {
            console.error(err);
            alert('⚠️ Failed to load user data.');
            modal.hide();
        });
}

/* ================================
   🗑️ DELETE USER
================================ */
function deleteUser(id, btn) {
    const row = btn.closest('tr');
    const name = row.querySelector('td:first-child')?.textContent || 'this user';
    if (!confirm(`Are you sure you want to delete "${name}"?`)) return;

    fetch(`/admin/users/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success) row.remove();
        })
        .catch(() => alert('Error deleting user'));
}

/* ================================
   🧾 REPORT & EXPORT MODULE
================================ */
export function exportTicketsPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    doc.setFontSize(18);
    doc.text('Ticket Report', 14, 22);
    doc.setFontSize(11);
    doc.text('Generated: ' + new Date().toLocaleDateString(), 14, 30);

    const table = document.getElementById('ticketsTable');
    if (!table) return alert('No ticket table found');

    const headers = [...table.querySelectorAll('thead th')].map(th => th.textContent);
    const rows = [...table.querySelectorAll('tbody tr')].map(tr =>
        [...tr.querySelectorAll('td')].map(td => td.textContent)
    );

    doc.autoTable({ head: [headers], body: rows, startY: 40, theme: 'grid' });
    doc.save('tickets_report.pdf');
}
document.addEventListener("DOMContentLoaded", () => {
    const userInfo = document.querySelector(".navbar-user .user-info");
    const dropdown = document.getElementById("userDropdown");

    if (userInfo && dropdown) {
        userInfo.addEventListener("click", () => {
            dropdown.classList.toggle("show");
        });

        // Tutup dropdown kalau klik di luar
        document.addEventListener("click", (e) => {
            if (!userInfo.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove("show");
            }
        });
    }
});

export function generateReport() {
    const reportType = document.getElementById('reportType')?.value || 'summary';
    const dateRange = document.getElementById('dateRange')?.value || 'N/A';

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    doc.setFontSize(20);
    doc.text(`${reportType.charAt(0).toUpperCase() + reportType.slice(1)} Report`, 14, 22);
    doc.setFontSize(12);
    doc.text(`Period: ${dateRange}`, 14, 32);
    doc.text(`Generated: ${new Date().toLocaleDateString()}`, 14, 40);
    doc.text('Report content coming soon...', 14, 55);
    doc.save(`${reportType}_report_${dateRange}.pdf`);
}

/* ================================
   ⚙️ EVENT LISTENERS
================================ */
document.addEventListener('DOMContentLoaded', () => {
    // Sidebar toggle
    document.querySelectorAll('.toggle-sidebar').forEach(btn =>
        btn.addEventListener('click', toggleSidebar)
    );

    // Menu item section change
    document.querySelectorAll('.menu-item').forEach(item => {
        item.addEventListener('click', function() {
            const section = this.getAttribute('data-section');
            showSection(section, this);
        });
    });

    // Open modal (Add User)
    document.querySelectorAll('.open-user-modal, #addUserBtn').forEach(btn =>
        btn.addEventListener('click', openUserModal)
    );

    // Close modal
    document.querySelectorAll('.close-user-modal').forEach(btn =>
        btn.addEventListener('click', closeUserModal)
    );

    // Edit/Delete user actions
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-user')) {
            const id = e.target.closest('.edit-user').dataset.id;
            editUser(id);
        }
        if (e.target.closest('.delete-user')) {
            const id = e.target.closest('.delete-user').dataset.id;
            deleteUser(id, e.target.closest('.delete-user'));
        }
    });

    // Report & export
    document.querySelectorAll('.export-pdf').forEach(btn =>
        btn.addEventListener('click', exportTicketsPDF)
    );
    document.querySelectorAll('.generate-report').forEach(btn =>
        btn.addEventListener('click', generateReport)
    );
});
