let tickets = [];        // Data asli dari server
let filteredTickets = []; 
let currentPage = 1;
const itemsPerPage = 10;

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    fetchTickets(); // ambil data dari backend
    setupEventListeners();
});

function fetchTickets() {
    fetch('/staff/tickets') // pastikan route sesuai
        .then(response => response.json())
        .then(data => {
            // Laravel paginate return ada .data
            tickets = data.data ?? data;  
            filteredTickets = [...tickets];
            renderTickets();
            updateStats();
            updateHeader();
        })
}

function setupEventListeners() {
    document.getElementById('searchInput')?.addEventListener('input', handleSearch);
    document.getElementById('statusFilter')?.addEventListener('change', handleFilter);
    document.getElementById('priorityFilter')?.addEventListener('change', handleFilter);
}

function handleSearch() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const priorityFilter = document.getElementById('priorityFilter').value;

    filteredTickets = tickets.filter(ticket => {
        const matchesSearch = ticket.description.toLowerCase().includes(searchTerm) ||
                              ticket.ticket_id.toLowerCase().includes(searchTerm);
        const matchesStatus = !statusFilter || ticket.status === statusFilter;
        const matchesPriority = !priorityFilter || ticket.priority === priorityFilter;

        return matchesSearch && matchesStatus && matchesPriority;
    });

    currentPage = 1;
    renderTickets();
    updateHeader();
}

function handleFilter() {
    handleSearch();
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
                <div class="ticket-id">${ticket.ticket_id}</div>
                <div class="ticket-info">
                    <div class="ticket-title">${ticket.category?.name ?? '-'}</div>
                    <div class="ticket-meta">
                        <div class="meta-item"><i class="fas fa-tag"></i>${ticket.category?.name ?? '-'}</div>
                        <div class="meta-item"><i class="fas fa-clock"></i>${ticket.created_at}</div>
                    </div>
                </div>
                <div class="priority-badge priority-${ticket.priority}">
                    ${getPriorityText(ticket.priority)}
                </div>
                <div class="status-badge status-${ticket.status}">
                    ${getStatusText(ticket.status)}
                </div>
            </div>
        </div>
    `).join('');
}

function updateStats() {
    const stats = {
        waiting: tickets.filter(t => t.status === 'waiting').length,
        progress: tickets.filter(t => t.status === 'progress').length,
        resolved: tickets.filter(t => t.status === 'resolved').length,
        closed: tickets.filter(t => t.status === 'closed').length
    };

    document.getElementById('openCount').textContent = stats.waiting;
    document.getElementById('progressCount').textContent = stats.progress;
    document.getElementById('resolvedCount').textContent = stats.resolved;
    document.getElementById('closedCount').textContent = stats.closed;
}

function updateHeader() {
    const header = document.querySelector('.tickets-header');
    if (header) {
        header.innerHTML = `<i class="fas fa-list"></i> Daftar Tiket (${filteredTickets.length} tiket ditemukan)`;
    }
}

function getStatusText(status) {
    const statusMap = {
        'waiting': 'Menunggu',
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

function viewTicketDetail(ticketId) {
    alert(`Membuka detail tiket: ${ticketId}`);
}
