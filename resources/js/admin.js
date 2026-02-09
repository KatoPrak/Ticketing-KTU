/* ================================
   🧱 ADMIN.JS - RESPONSIVE VERSION
================================ */

// ================================
// GLOBAL VARIABLES
// ================================
let currentEditUserId = null;

// ================================
// SIDEBAR FUNCTIONS - MOBILE RESPONSIVE
// ================================
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    if (sidebar) {
        sidebar.classList.toggle('active');

        // Create overlay if doesn't exist
        if (!overlay) {
            const newOverlay = document.createElement('div');
            newOverlay.className = 'sidebar-overlay';
            newOverlay.addEventListener('click', closeSidebar);
            document.body.appendChild(newOverlay);
        }

        const currentOverlay = document.querySelector('.sidebar-overlay');
        if (currentOverlay) {
            currentOverlay.classList.toggle('active');
        }
    }
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    if (sidebar) {
        sidebar.classList.remove('active');
    }
    if (overlay) {
        overlay.classList.remove('active');
    }
}

// ================================
// USER MODAL FUNCTIONS
// ================================
function openUserModal() {
    console.log('📝 Opening modal for ADD user');

    const modalEl = document.getElementById('userModal');
    const form = document.getElementById('userForm');
    const title = document.getElementById('modalTitle');

    if (!modalEl || !form) {
        console.error('❌ Modal or form not found!');
        return;
    }

    // Reset form
    form.reset();
    form.action = '/admin/users';
    title.innerHTML = '<i class="fas fa-user-plus me-2"></i> Add New User';

    // Remove _method input if exists (for add mode)
    const methodInput = form.querySelector('input[name="_method"]');
    if (methodInput) {
        methodInput.remove();
    }

    // Reset user_id hidden field
    const userIdInput = form.querySelector('#user_id');
    if (userIdInput) {
        userIdInput.value = '';
    }

    // Clear password requirement message
    const passwordHelp = form.querySelector('#passwordHelp');
    if (passwordHelp) {
        passwordHelp.textContent = 'Leave blank to keep current password';
        passwordHelp.style.display = 'none';
    }

    // Make password required for new user
    const passwordInput = form.querySelector('#password');
    if (passwordInput) {
        passwordInput.required = true;
    }

    currentEditUserId = null;

    // Show modal
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

function closeUserModal() {
    const modalEl = document.getElementById('userModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
        }
    }
}

// ================================
// EDIT USER FUNCTION
// ================================
function editUser(id) {
    console.log('✏️ Editing user:', id);

    const modalEl = document.getElementById('userModal');
    const form = document.getElementById('userForm');
    const title = document.getElementById('modalTitle');

    if (!modalEl || !form) {
        console.error('❌ Modal or form not found!');
        return;
    }

    // Set modal title
    title.innerHTML = '<i class="fas fa-user-edit me-2"></i> Edit User';

    // Set form action to update route
    form.action = `/admin/users/${id}`;

    // Add or update _method input for PUT request
    let methodInput = form.querySelector('input[name="_method"]');
    if (!methodInput) {
        methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        form.appendChild(methodInput);
    }
    methodInput.value = 'PUT';

    // Store current edit user id
    currentEditUserId = id;

    // Show modal with loading state
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();

    // Add loading overlay
    const modalBody = form.closest('.modal-body');
    if (modalBody) {
        modalBody.style.opacity = '0.5';
        modalBody.style.pointerEvents = 'none';
    }

    // Fetch user data
    fetch(`/admin/users/${id}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(user => {
            console.log('✅ User data loaded:', user);

            // Populate form fields
            const userIdInput = form.querySelector('#user_id');
            const nameInput = form.querySelector('#name');
            const idStaffInput = form.querySelector('#id_staff');
            const emailInput = form.querySelector('#email');
            const roleSelect = form.querySelector('#role');
            const departmentSelect = form.querySelector('#department_id');
            const passwordInput = form.querySelector('#password');
            const passwordHelp = form.querySelector('#passwordHelp');

            if (userIdInput) userIdInput.value = user.id || '';
            if (nameInput) nameInput.value = user.name || '';
            if (idStaffInput) idStaffInput.value = user.id_staff || '';
            if (emailInput) emailInput.value = user.email || '';
            if (roleSelect) roleSelect.value = user.role || '';
            if (departmentSelect) departmentSelect.value = user.department_id || '';

            // Handle Location Field
            const locationField = document.getElementById('locationField');
            const locationSelect = document.getElementById('location_id');
            const regionSelect = document.getElementById('region_id');

            // Set values
            if (locationSelect) locationSelect.value = user.location_id || '';
            if (regionSelect) regionSelect.value = user.region_id || '';

            // Update visibility based on loaded role
            if (typeof updateFieldsVisibility === 'function') {
                updateFieldsVisibility();
            } else {
                // Fallback if function not yet defined (rare)
                const roleSelect = form.querySelector('#role');
                if (roleSelect) roleSelect.dispatchEvent(new Event('change'));
            }

            // Make password optional for edit
            if (passwordInput) {
                passwordInput.required = false;
                passwordInput.value = '';
            }

            // Show password help text
            if (passwordHelp) {
                passwordHelp.textContent = 'Leave blank to keep current password';
                passwordHelp.style.display = 'block';
            }

            // Remove loading state
            if (modalBody) {
                modalBody.style.opacity = '1';
                modalBody.style.pointerEvents = 'auto';
            }
        })
        .catch(error => {
            console.error('❌ Error loading user:', error);
            alert('Failed to load user data. Please try again.');
            modal.hide();
        });
}

// ================================
// DELETE USER FUNCTION
// ================================
function deleteUser(id, btn) {
    const row = btn.closest('tr');
    const nameCell = row.querySelector('td:first-child');
    const name = nameCell ? nameCell.textContent.trim() : 'this user';

    // Show confirmation dialog
    if (!confirm(`⚠️ Are you sure you want to delete "${name}"?\n\nThis action cannot be undone!`)) {
        return;
    }

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('❌ CSRF token not found!');
        return;
    }

    // Disable button during deletion
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    // Send delete request
    fetch(`/admin/users/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken.content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove row with animation
                row.style.transition = 'opacity 0.3s';
                row.style.opacity = '0';
                setTimeout(() => {
                    row.remove();

                    // Check if table is empty
                    const tbody = document.querySelector('#usersTable tbody');
                    const visibleRows = tbody.querySelectorAll('tr:not([style*="display: none"])');
                    if (visibleRows.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No users found</td></tr>';
                    }
                }, 300);

                // Show success message
                showAlert('success', data.message || 'User deleted successfully!');
            } else {
                throw new Error(data.message || 'Failed to delete user');
            }
        })
        .catch(error => {
            console.error('❌ Delete error:', error);
            alert('Error: ' + error.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-trash"></i>';
        });
}

// ================================
// SEARCH FUNCTIONALITY
// ================================
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('usersTable');

    if (!searchInput || !table) return;

    searchInput.addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase().trim();
        const rows = table.querySelectorAll('tbody tr:not(#noUsersRow):not(#noResultsRow)');
        const noResultsRow = document.getElementById('noResultsRow');
        let visibleCount = 0;

        rows.forEach(row => {
            const staffIdCell = row.querySelector('.staff-id');
            const nameCell = row.querySelector('td:first-child');
            const emailCell = row.querySelector('td:nth-child(3)');

            const staffId = staffIdCell ? staffIdCell.textContent.toLowerCase() : '';
            const name = nameCell ? nameCell.textContent.toLowerCase() : '';
            const email = emailCell ? emailCell.textContent.toLowerCase() : '';

            const matches = staffId.includes(searchTerm) ||
                name.includes(searchTerm) ||
                email.includes(searchTerm);

            if (matches) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide no results message
        if (noResultsRow) {
            noResultsRow.style.display = (visibleCount === 0 && searchTerm !== '') ? '' : 'none';
        }
    });
}

// ================================
// ALERT FUNCTION
// ================================
function showAlert(type, message) {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    // Insert at top of content
    const content = document.querySelector('.content');
    if (content) {
        content.insertBefore(alertDiv, content.firstChild);

        // Auto dismiss after 5 seconds
        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 150);
        }, 5000);
    }
}



// ================================
// NAVBAR HAMBURGER TOGGLE
// ================================
function initializeNavbarToggle() {
    const navbar = document.querySelector('.navbar');

    if (navbar && window.innerWidth <= 768) {
        navbar.addEventListener('click', function (e) {
            // Check if clicked on the ::before pseudo element area
            const rect = this.getBoundingClientRect();
            if (e.clientX - rect.left < 40 && e.clientY - rect.top < 50) {
                toggleSidebar();
            }
        });
    }
}

// ================================
// MAIN INITIALIZATION
// ================================
document.addEventListener('DOMContentLoaded', function () {
    console.log('🚀 Admin.js initializing...');

    // Initialize search functionality
    initializeSearch();



    // Initialize navbar toggle for mobile
    initializeNavbarToggle();

    // Sidebar toggle buttons
    document.querySelectorAll('.toggle-sidebar, .toggle-btn').forEach(btn => {
        btn.addEventListener('click', toggleSidebar);
    });

    // Menu item clicks
    document.querySelectorAll('.menu-item').forEach(item => {
        item.addEventListener('click', function (e) {
            const section = this.getAttribute('data-section');

            // If menu item has href, let it navigate normally
            if (this.hasAttribute('href') && this.getAttribute('href') !== '#') {
                // Just close sidebar on mobile, let the link work
                if (window.innerWidth <= 768) {
                    closeSidebar();
                }
                // Don't prevent default, let the link navigate
                return;
            }

            // Only use showSection for data-section based navigation
            if (section) {
                e.preventDefault();
                showSection(section, this);
            }
        });
    });

    // Open modal (Add User)
    const addUserButtons = document.querySelectorAll('.open-user-modal, #addUserBtn, [data-action="add-user"]');
    addUserButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            openUserModal();
        });
    });

    // Close modal
    document.querySelectorAll('.close-user-modal').forEach(btn => {
        btn.addEventListener('click', closeUserModal);
    });

    // Event delegation for edit/delete buttons
    document.addEventListener('click', function (e) {
        // Edit user
        const editBtn = e.target.closest('.edit-user');
        if (editBtn) {
            e.preventDefault();
            const id = editBtn.dataset.id;
            if (id) {
                editUser(id);
            }
        }

        // Delete user
        const deleteBtn = e.target.closest('.delete-user');
        if (deleteBtn) {
            e.preventDefault();
            const id = deleteBtn.dataset.id;
            if (id) {
                deleteUser(id, deleteBtn);
            }
        }
    });

    // Auto-dismiss alerts after 5 seconds
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Handle window resize
    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) {
            closeSidebar();
        }
    });

    // Role change event to toggle location/region fields
    const roleSelect = document.getElementById('role');
    const locationField = document.getElementById('locationField');
    const regionField = document.getElementById('regionField');
    const locationSelect = document.getElementById('location_id');
    const regionSelect = document.getElementById('region_id');

    function updateFieldsVisibility() {
        if (!roleSelect) return;

        const role = roleSelect.value;
        const isIT = (role === 'tim it' || role === 'it');
        const isUser = (role === 'user' || role === 'staff');

        // Logic based on requirements:
        // User -> Location (Required), Region (Hidden)
        // IT -> Region (Required), Location (Hidden or Optional?) 
        // User said: "Staff IT tidak pegang lokasi, tapi pegang REGIONAL"

        if (isIT) {
            if (regionField) regionField.style.display = 'block';
            if (locationField) locationField.style.display = 'none';

            if (regionSelect) regionSelect.required = true;
            if (locationSelect) {
                locationSelect.required = false;
                locationSelect.value = ''; // Clear location for IT
            }
        } else {
            // Default (User/Staff)
            if (regionField) regionField.style.display = 'none';
            if (locationField) locationField.style.display = 'block';

            if (regionSelect) {
                regionSelect.required = false;
                regionSelect.value = ''; // Clear region for User
            }
            if (locationSelect) locationSelect.required = true;
        }
    }

    if (roleSelect) {
        roleSelect.addEventListener('change', updateFieldsVisibility);
        // Initial check
        updateFieldsVisibility();
    }

    // Ensure backdrop and form are cleaned up when modal closes
    const userModalEl = document.getElementById('userModal');
    if (userModalEl) {
        userModalEl.addEventListener('hidden.bs.modal', function () {
            const form = document.getElementById('userForm');
            if (form) form.reset();
            currentEditUserId = null;

            // Reset visibility
            updateFieldsVisibility();

            // Remove any stuck backdrops

            // Remove any stuck backdrops
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
    }

    console.log('✅ Admin.js loaded successfully');
});

// Make functions globally accessible
window.openUserModal = openUserModal;
window.closeUserModal = closeUserModal;
window.editUser = editUser;
window.deleteUser = deleteUser;
window.toggleSidebar = toggleSidebar;