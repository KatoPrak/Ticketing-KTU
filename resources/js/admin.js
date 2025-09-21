// Toggle Sidebar
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('collapsed');
}

// Show Section
function showSection(sectionName, menuItem) {
    // Hide all sections
    const sections = document.querySelectorAll('.content-section');
    sections.forEach(section => {
        section.classList.remove('active');
    });

    // Show selected section
    const selectedSection = document.getElementById(sectionName);
    if (selectedSection) {
        selectedSection.classList.add('active');
    }

    // Update menu items
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach(item => {
        item.classList.remove('active');
    });
    if (menuItem) {
        menuItem.classList.add('active');
    }
}

// User Modal Functions
function openUserModal() {
    document.getElementById('userModal').classList.add('show');
}

function closeUserModal() {
    document.getElementById('userModal').classList.remove('show');
    document.getElementById('userForm').reset();
}

function saveUser() {
    const name = document.getElementById('userName').value;
    const email = document.getElementById('userEmail').value;
    const role = document.getElementById('userRole').value;
    const dept = document.getElementById('userDept').value;

    if (name && email) {
        // Add new row to table
        const table = document.getElementById('usersTable').getElementsByTagName('tbody')[0];
        const newRow = table.insertRow();
        const userId = Date.now();
        newRow.innerHTML = `
            <td>${name}</td>
            <td>${email}</td>
            <td>${role.charAt(0).toUpperCase() + role.slice(1)}</td>
            <td>${dept.charAt(0).toUpperCase() + dept.slice(1)}</td>
            <td><span class="badge badge-success">Active</span></td>
            <td>
                <button class="btn btn-sm btn-primary edit-user" data-id="${userId}">Edit</button>
                <button class="btn btn-sm btn-danger delete-user" data-id="${userId}">Delete</button>
            </td>
        `;
        closeUserModal();
        alert('User added successfully!');
    }
}
// Export Tickets to PDF
function exportTicketsPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Add title
    doc.setFontSize(18);
    doc.text('Ticket Report', 14, 22);
    
    // Add date
    doc.setFontSize(11);
    doc.text('Generated: ' + new Date().toLocaleDateString(), 14, 30);

    // Get table data
    const table = document.getElementById('ticketsTable');
    const headers = [];
    const data = [];

    // Get headers
    const headerCells = table.getElementsByTagName('thead')[0].getElementsByTagName('th');
    for (let cell of headerCells) {
        headers.push(cell.textContent);
    }

    // Get data
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    for (let row of rows) {
        const rowData = [];
        const cells = row.getElementsByTagName('td');
        for (let cell of cells) {
            rowData.push(cell.textContent);
        }
        data.push(rowData);
    }

    // Add table to PDF
    doc.autoTable({
        head: [headers],
        body: data,
        startY: 40,
        theme: 'grid',
        styles: { fontSize: 9 },
        headStyles: { fillColor: [44, 62, 80] }
    });

    // Save PDF
    doc.save('tickets_report.pdf');
}

// Generate Report
function generateReport() {
    const reportType = document.getElementById('reportType').value;
    const dateRange = document.getElementById('dateRange').value;
    
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Add title
    doc.setFontSize(20);
    doc.text(`${reportType.charAt(0).toUpperCase() + reportType.slice(1)} Report`, 14, 22);
    
    // Add date range
    doc.setFontSize(12);
    doc.text(`Period: ${dateRange}`, 14, 32);
    doc.text(`Generated: ${new Date().toLocaleDateString()}`, 14, 40);

    // Add content based on report type
    doc.setFontSize(11);
    if (reportType === 'tickets') {
        doc.text('Total Tickets: 157', 14, 55);
        doc.text('Resolved: 89', 14, 63);
        doc.text('In Progress: 56', 14, 71);
        doc.text('Pending: 12', 14, 79);
    } else if (reportType === 'users') {
        doc.text('Total Users: 24', 14, 55);
        doc.text('Staff: 18', 14, 63);
        doc.text('IT Team: 4', 14, 71);
        doc.text('Admins: 2', 14, 79);
    } else {
        doc.text('System Summary', 14, 55);
        doc.text('Total Users: 24', 14, 63);
        doc.text('Total Tickets: 157', 14, 71);
        doc.text('Average Resolution Time: 2.5 days', 14, 79);
        doc.text('Customer Satisfaction: 92%', 14, 87);
    }

    // Save PDF
    doc.save(`${reportType}_report_${dateRange}.pdf`);
    alert('Report generated successfully!');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('userModal');
    if (event.target == modal) {
        closeUserModal();
    }
};

/* ================================
    EVENT LISTENER BAGIAN INI
================================ */

// Toggle sidebar button
document.querySelectorAll('.toggle-sidebar').forEach(btn => {
    btn.addEventListener('click', toggleSidebar);
});

// Menu items
document.querySelectorAll('.menu-item').forEach(item => {
    item.addEventListener('click', function() {
        const section = this.getAttribute('data-section');
        showSection(section, this);
    });
});

// Open user modal
document.querySelectorAll('.open-user-modal').forEach(btn => {
    btn.addEventListener('click', openUserModal);
});

// Close user modal
document.querySelectorAll('.close-user-modal').forEach(btn => {
    btn.addEventListener('click', closeUserModal);
});

// Toggle User Dropdown
document.querySelectorAll('.user-info').forEach(user => {
    user.addEventListener('click', function(e) {
        e.stopPropagation(); // cegah close langsung
        document.getElementById('userDropdown').classList.toggle('show');
    });
});

// Tutup dropdown kalau klik di luar
window.addEventListener("click", function(event) {
    const dropdown = document.getElementById("userDropdown");
    if (!event.target.closest(".navbar-user")) {
        dropdown.classList.remove("show");
    }
});


// Save user
document.querySelectorAll('.save-user').forEach(btn => {
    btn.addEventListener('click', saveUser);
});

// Edit & delete user (delegation)
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('edit-user')) {
        const id = e.target.getAttribute('data-id');
        editUser(id);
    }
    if (e.target.classList.contains('delete-user')) {
        const id = e.target.getAttribute('data-id');
        deleteUser(id, e.target);
    }
});

// Export PDF
document.querySelectorAll('.export-pdf').forEach(btn => {
    btn.addEventListener('click', exportTicketsPDF);
});

// Generate report
document.querySelectorAll('.generate-report').forEach(btn => {
    btn.addEventListener('click', generateReport);
});
