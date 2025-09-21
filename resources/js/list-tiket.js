// Sample ticket data
const sampleTickets = [
    {
        id: 'TKT-2024-001',
        title: 'Tidak bisa login ke sistem',
        description: 'Saya mengalami kesulitan untuk login ke sistem menggunakan username dan password yang benar',
        status: 'open',
        priority: 'high',
        category: 'Technical',
        createdDate: '2024-09-15',
        updatedDate: '2024-09-15',
        assignedTo: 'Admin System'
    },
    {
        id: 'TKT-2024-002',
        title: 'Permintaan akses ke modul keuangan',
        description: 'Membutuhkan akses ke modul keuangan untuk dapat mengakses laporan bulanan',
        status: 'progress',
        priority: 'medium',
        category: 'Access Request',
        createdDate: '2024-09-14',
        updatedDate: '2024-09-16',
        assignedTo: 'HR Department'
    },
    {
        id: 'TKT-2024-003',
        title: 'Komputer tidak bisa menyala',
        description: 'Komputer di meja kerja saya tidak bisa menyala sama sekali setelah weekend',
        status: 'resolved',
        priority: 'high',
        category: 'Hardware',
        createdDate: '2024-09-13',
        updatedDate: '2024-09-15',
        assignedTo: 'IT Support'
    },
    {
        id: 'TKT-2024-004',
        title: 'Instalasi software baru',
        description: 'Membutuhkan instalasi Adobe Photoshop untuk keperluan desain marketing',
        status: 'progress',
        priority: 'low',
        category: 'Software',
        createdDate: '2024-09-12',
        updatedDate: '2024-09-14',
        assignedTo: 'IT Support'
    },
    {
        id: 'TKT-2024-005',
        title: 'Email tidak bisa mengirim attachment',
        description: 'Ketika mencoba mengirim email dengan attachment, selalu gagal dan muncul error',
        status: 'open',
        priority: 'medium',
        category: 'Email',
        createdDate: '2024-09-11',
        updatedDate: '2024-09-11',
        assignedTo: 'IT Support'
    },
    {
        id: 'TKT-2024-006',
        title: 'Permintaan pembuatan akun baru',
        description: 'Karyawan baru membutuhkan akun sistem untuk dapat bekerja',
        status: 'resolved',
        priority: 'high',
        category: 'Account',
        createdDate: '2024-09-10',
        updatedDate: '2024-09-12',
        assignedTo: 'Admin System'
    },
    {
        id: 'TKT-2024-007',
        title: 'Printer tidak berfungsi',
        description: 'Printer di lantai 2 tidak bisa mencetak dan menampilkan pesan error',
        status: 'progress',
        priority: 'medium',
        category: 'Hardware',
        createdDate: '2024-09-09',
        updatedDate: '2024-09-15',
        assignedTo: 'IT Support'
    },
    {
        id: 'TKT-2024-008',
        title: 'Update password expired',
        description: 'Password sistem sudah expired dan perlu di-reset',
        status: 'resolved',
        priority: 'medium',
        category: 'Account',
        createdDate: '2024-09-08',
        updatedDate: '2024-09-09',
        assignedTo: 'Admin System'
    },
    {
        id: 'TKT-2024-009',
        title: 'Jaringan internet lambat',
        description: 'Koneksi internet di ruangan marketing sangat lambat dan sering terputus',
        status: 'open',
        priority: 'high',
        category: 'Network',
        createdDate: '2024-09-07',
        updatedDate: '2024-09-07',
        assignedTo: 'Network Admin'
    },
    {
        id: 'TKT-2024-010',
        title: 'Backup data mingguan',
        description: 'Permintaan backup data project untuk arsip mingguan',
        status: 'closed',
        priority: 'low',
        category: 'Data',
        createdDate: '2024-09-05',
        updatedDate: '2024-09-06',
        assignedTo: 'Data Admin'
    },
    {
        id: 'TKT-2024-011',
        title: 'Training penggunaan sistem baru',
        description: 'Membutuhkan training untuk menggunakan sistem CRM yang baru diimplementasi',
        status: 'open',
        priority: 'medium',
        category: 'Training',
        createdDate: '2024-09-04',
        updatedDate: '2024-09-04',
        assignedTo: 'Training Team'
    },
    {
        id: 'TKT-2024-012',
        title: 'Migrasi data dari sistem lama',
        description: 'Perlu bantuan untuk migrasi data customer dari sistem lama ke sistem baru',
        status: 'open',
        priority: 'high',
        category: 'Data Migration',
        createdDate: '2024-09-03',
        updatedDate: '2024-09-03',
        assignedTo: 'Data Migration Team'
    }
];

let filteredTickets = [...sampleTickets];
let currentPage = 1;
const itemsPerPage = 10;

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    renderTickets();
    updateStats();
    setupEventListeners();

    // Animasi masuk untuk card statistik
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.offsetHeight; // Trigger reflow
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

function setupEventListeners() {
    // Search functionality
    document.getElementById('searchInput')?.addEventListener('input', handleSearch);

    // Filter functionality
    document.getElementById('statusFilter')?.addEventListener('change', handleFilter);
    document.getElementById('priorityFilter')?.addEventListener('change', handleFilter);
}

function handleSearch() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const priorityFilter = document.getElementById('priorityFilter').value;

    filteredTickets = sampleTickets.filter(ticket => {
        const matchesSearch = ticket.title.toLowerCase().includes(searchTerm) || 
                            ticket.id.toLowerCase().includes(searchTerm) ||
                            ticket.description.toLowerCase().includes(searchTerm);
        const matchesStatus = !statusFilter || ticket.status === statusFilter;
        const matchesPriority = !priorityFilter || ticket.priority === priorityFilter;

        return matchesSearch && matchesStatus && matchesPriority;
    });

    currentPage = 1;
    renderTickets();
    updateHeader();
}

function handleFilter() {
    handleSearch(); // Reuse search logic as it handles all filters
}

function renderTickets() {
    const ticketsList = document.getElementById('ticketsList');
    const emptyState = document.getElementById('emptyState');
    
    if (filteredTickets.length === 0) {
        ticketsList.innerHTML = '';
        if (emptyState) emptyState.style.display = 'block';
        return;
    }

    if (emptyState) emptyState.style.display = 'none';

    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = Math.min(startIndex + itemsPerPage, filteredTickets.length);
    const ticketsToShow = filteredTickets.slice(startIndex, endIndex);

    ticketsList.innerHTML = ticketsToShow.map(ticket => `
        <div class="ticket-item" onclick="viewTicketDetail('${ticket.id}')">
            <div class="ticket-row">
                <div class="ticket-id">${ticket.id}</div>
                <div class="ticket-info">
                    <div class="ticket-title">${ticket.title}</div>
                    <div class="ticket-meta">
                        <div class="meta-item">
                            <i class="fas fa-tag"></i>
                            ${ticket.category}
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-user"></i>
                            ${ticket.assignedTo}
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-clock"></i>
                            ${formatDate(ticket.updatedDate)}
                        </div>
                    </div>
                </div>
                <div class="priority-badge priority-${ticket.priority}">
                    ${getPriorityText(ticket.priority)}
                </div>
                <div class="status-badge status-${ticket.status}">
                    ${getStatusText(ticket.status)}
                </div>
                <div class="ticket-date">
                    <div style="font-weight: 600;">${formatDate(ticket.createdDate)}</div>
                    <div style="font-size: 0.8rem;">Dibuat</div>
                </div>
            </div>
        </div>
    `).join('');
}

function updateStats() {
    const stats = {
        open: sampleTickets.filter(t => t.status === 'open').length,
        progress: sampleTickets.filter(t => t.status === 'progress').length,
        resolved: sampleTickets.filter(t => t.status === 'resolved').length,
        closed: sampleTickets.filter(t => t.status === 'closed').length
    };

    const openEl = document.getElementById('openCount');
    const progressEl = document.getElementById('progressCount');
    const resolvedEl = document.getElementById('resolvedCount');
    const closedEl = document.getElementById('closedCount');

    if (openEl) openEl.textContent = stats.open;
    if (progressEl) progressEl.textContent = stats.progress;
    if (resolvedEl) resolvedEl.textContent = stats.resolved;
    if (closedEl) closedEl.textContent = stats.closed;
}

function updateHeader() {
    const header = document.querySelector('.tickets-header');
    if (header) {
        header.innerHTML = `<i class="fas fa-list"></i> Daftar Tiket (${filteredTickets.length} tiket ditemukan)`;
    }
}

function getStatusText(status) {
    const statusMap = {
        'open': 'Terbuka',
        'progress': 'Dalam Proses',
        'resolved': 'Selesai',
        'closed': 'Ditutup'
    };
    return statusMap[status] || status;
}

function getPriorityText(priority) {
    const priorityMap = {
        'high': 'Tinggi',
        'medium': 'Sedang',
        'low': 'Rendah'
    };
    return priorityMap[priority] || priority;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function viewTicketDetail(ticketId) {
    // Simulate navigation to ticket detail page
    alert(`Membuka detail tiket: ${ticketId}\n\nFitur ini akan mengarahkan ke halaman detail tiket.`);
}

function createNewTicket() {
    // Show modal form tiket
    const modalEl = document.getElementById('createTicketModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    } else {
        alert('Modal form tiket belum ada di halaman ini.');
    }
}
