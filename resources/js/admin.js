/* ================================
   🧱 ADMIN.JS - RESPONSIVE VERSION
   ================================ */

// ================================
// GLOBAL VARIABLES
// ================================
let currentEditUserId = null;

// ================================
// HELPER FUNCTIONS (GLOBAL SCOPE)
// ================================
function updateFieldsVisibility() {
    const roleSelect = document.getElementById('role');
    const locationField = document.getElementById('locationField');
    const regionField = document.getElementById('regionField');
    const locationSelect = document.getElementById('location_id');
    const regionSelect = document.getElementById('region_id');
    const emailItNote = document.getElementById('emailItNote');

    if (!roleSelect) return;

    const role = roleSelect.value;
    const isIT = (role === 'tim it' || role === 'it');

    if (isIT) {
        // IT Role: Region is Assignment (Required), Location is Hidden
        if (regionField) regionField.style.display = 'block';
        if (locationField) locationField.style.display = 'none';
        if (emailItNote) emailItNote.style.display = 'block';

        if (regionSelect) regionSelect.required = true;
        if (locationSelect) {
            locationSelect.required = false;
            locationSelect.value = '';
        }
    } else {
        // User Role: Region is Filter (Required to select Location), Location is Required
        if (regionField) regionField.style.display = 'block';
        if (emailItNote) emailItNote.style.display = 'none';

        // Hide Location Initially until region selected
        if (locationField) {
            if (regionSelect && regionSelect.value) {
                locationField.style.display = 'block';
            } else {
                locationField.style.display = 'none';
            }
        }

        if (regionSelect) regionSelect.required = true;
        if (locationSelect) locationSelect.required = true;
    }
}

function filterLocationsByRegion() {
    const regionSelect = document.getElementById('region_id');
    const locationSelect = document.getElementById('location_id');
    const locationField = document.getElementById('locationField');
    const roleSelect = document.getElementById('role');

    if (!regionSelect || !locationSelect) return;

    const selectedRegionId = regionSelect.value;
    const options = locationSelect.querySelectorAll('option');

    // Update visibility logic based on selection change
    if (locationField && roleSelect) {
        const role = roleSelect.value;
        const isIT = (role === 'tim it' || role === 'it');
        if (!isIT) {
            if (selectedRegionId) {
                locationField.style.display = 'block';
            } else {
                locationField.style.display = 'none';
            }
        }
    }

    // Filter Options
    const currentLocOption = locationSelect.options[locationSelect.selectedIndex];

    options.forEach(opt => {
        if (!opt.value) return; // Skip placeholder

        const regionId = opt.getAttribute('data-region-id');
        if (selectedRegionId && regionId === selectedRegionId) {
            opt.style.display = ''; // Reset to default
            opt.hidden = false;
        } else {
            opt.style.display = 'none';
            opt.hidden = true;
        }
    });

    // Reset value if currently selected is now hidden
    if (currentLocOption && currentLocOption.value && currentLocOption.hidden) {
        locationSelect.value = "";
    }
}

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
    if (methodInput) methodInput.remove();

    // Reset user_id hidden field
    const userIdInput = form.querySelector('#user_id');
    if (userIdInput) userIdInput.value = '';

    currentEditUserId = null;

    // Trigger visibility update and filter reset
    updateFieldsVisibility();

    // Clear filters initially (since form.reset() clears region)
    const regionSelect = document.getElementById('region_id');
    const locationSelect = document.getElementById('location_id');
    if (regionSelect) regionSelect.value = "";
    if (locationSelect) locationSelect.value = "";

    filterLocationsByRegion();

    // ── PASSWORD: show ADD section, hide EDIT section ──
    document.getElementById('passwordAddSection').style.display = 'block';
    document.getElementById('passwordEditSection').style.display = 'none';

    // Reset to default password mode
    const useDefault = document.getElementById('useDefaultPassword');
    const manualField = document.getElementById('manualPasswordField');
    const defaultInput = document.getElementById('defaultPasswordInput');
    const passwordInput = document.getElementById('password');

    if (useDefault) useDefault.checked = true;
    if (manualField) manualField.style.display = 'none';
    if (defaultInput) defaultInput.value = 'STAFFKTU123';
    if (passwordInput) passwordInput.value = '';

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
            const nikInput = form.querySelector('#nik');
            const usernameInput = form.querySelector('#username');
            const emailInput = form.querySelector('#email');
            const roleSelect = form.querySelector('#role');
            const departmentSelect = form.querySelector('#department_id');

            if (userIdInput) userIdInput.value = user.id || '';
            if (nameInput) nameInput.value = user.name || '';
            if (nikInput) nikInput.value = user.nik || '';
            if (usernameInput) usernameInput.value = user.username || '';
            if (emailInput) emailInput.value = user.email || '';
            if (roleSelect) roleSelect.value = user.role || '';
            if (departmentSelect) departmentSelect.value = user.department_id || '';

            // Handle Location Field & Region Field logic
            const locationSelect = document.getElementById('location_id');
            const regionSelect = document.getElementById('region_id');

            // 1. Set Role first to determine visibility mode
            if (roleSelect) {
                roleSelect.value = user.role || '';
                updateFieldsVisibility();
            }

            // 2. Determine Region to select
            let regionToSelect = user.region_id;

            // If no explicit region (e.g. Regular User), derive it from Location
            if (!regionToSelect && user.location_id && locationSelect) {
                // Find the option for this location
                const locOption = locationSelect.querySelector(`option[value="${user.location_id}"]`);
                if (locOption) {
                    regionToSelect = locOption.getAttribute('data-region-id');
                }
            }

            // 3. Set Region Value
            if (regionSelect) {
                regionSelect.value = regionToSelect || '';
                // 4. Trigger Filter based on the set region
                filterLocationsByRegion();
            }

            // 5. Set Location Value (after filter, so options are valid)
            if (locationSelect) {
                locationSelect.value = user.location_id || '';
            }

            // ── PASSWORD: switch to EDIT section ──
            document.getElementById('passwordAddSection').style.display = 'none';
            document.getElementById('passwordEditSection').style.display = 'block';

            // Reset change-password checkbox
            const changePasswordCb = document.getElementById('changePassword');
            const editPasswordField = document.getElementById('editPasswordField');
            const editPasswordInput = document.getElementById('editPassword');
            if (changePasswordCb) changePasswordCb.checked = false;
            if (editPasswordField) editPasswordField.style.display = 'none';
            if (editPasswordInput) {
                editPasswordInput.disabled = false;
                editPasswordInput.value = '';
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
            const nikCell = row.querySelector('.nik');
            const usernameCell = row.querySelector('.username');
            const nameCell = row.querySelector('td:first-child');
            const emailCell = row.querySelector('td:nth-child(3)');

            const nik = nikCell ? nikCell.textContent.toLowerCase() : '';
            const username = usernameCell ? usernameCell.textContent.toLowerCase() : '';
            const name = nameCell ? nameCell.textContent.toLowerCase() : '';
            const email = emailCell ? emailCell.textContent.toLowerCase() : '';

            const matches = nik.includes(searchTerm) ||
                username.includes(searchTerm) ||
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
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    const content = document.querySelector('.content');
    if (content) {
        content.insertBefore(alertDiv, content.firstChild);
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
            if (this.hasAttribute('href') && this.getAttribute('href') !== '#') {
                if (window.innerWidth <= 768) {
                    closeSidebar();
                }
                return;
            }
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

    // Auto-dismiss alerts
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

    // Role change event
    const roleSelect = document.getElementById('role');
    const regionSelect = document.getElementById('region_id');

    if (roleSelect) {
        roleSelect.addEventListener('change', () => {
            updateFieldsVisibility();
        });
        updateFieldsVisibility();
    }

    if (regionSelect) {
        regionSelect.addEventListener('change', filterLocationsByRegion);
    }

    // ── PASSWORD CHECKBOX LOGIC ──
    const useDefaultCb = document.getElementById('useDefaultPassword');
    const manualPasswordField = document.getElementById('manualPasswordField');
    const defaultPasswordInput = document.getElementById('defaultPasswordInput');
    const manualPasswordInput = document.getElementById('password');

    const changePasswordCb = document.getElementById('changePassword');
    const editPasswordField = document.getElementById('editPasswordField');
    const editPasswordInput = document.getElementById('editPassword');

    if (useDefaultCb) {
        useDefaultCb.addEventListener('change', function () {
            if (this.checked) {
                manualPasswordField.style.display = 'none';
                manualPasswordInput.value = '';
                defaultPasswordInput.value = 'STAFFKTU123';
            } else {
                manualPasswordField.style.display = 'block';
                defaultPasswordInput.value = '';
                manualPasswordInput.focus();
            }
        });
        if (manualPasswordInput) {
            manualPasswordInput.addEventListener('input', function () {
                if (!useDefaultCb.checked) {
                    defaultPasswordInput.value = this.value;
                }
            });
        }
    }

    const toggleVisibilityCb = document.getElementById('togglePasswordVisibility');
    if (toggleVisibilityCb && manualPasswordInput) {
        toggleVisibilityCb.addEventListener('change', function () {
            manualPasswordInput.type = this.checked ? 'text' : 'password';
        });
    }

    if (changePasswordCb) {
        changePasswordCb.addEventListener('change', function () {
            if (this.checked) {
                editPasswordField.style.display = 'block';
                editPasswordInput.disabled = false;
                editPasswordInput.focus();
            } else {
                editPasswordField.style.display = 'none';
                editPasswordInput.value = '';
                editPasswordInput.disabled = true;
                defaultPasswordInput.value = '';
            }
        });
        if (editPasswordInput) {
            editPasswordInput.addEventListener('input', function () {
                if (changePasswordCb.checked) {
                    defaultPasswordInput.value = this.value;
                }
            });
        }
    }

    const toggleEditVisibilityCb = document.getElementById('toggleEditPasswordVisibility');
    if (toggleEditVisibilityCb && editPasswordInput) {
        toggleEditVisibilityCb.addEventListener('change', function () {
            editPasswordInput.type = this.checked ? 'text' : 'password';
        });
    }

    const generateBtnAdd = document.getElementById('generatePasswordAdminAdd');
    const generateBtnEdit = document.getElementById('generatePasswordAdminEdit');
    const manualPassInput = document.getElementById('password');
    const editPassInput_gen = document.getElementById('editPassword');

    const generateAndSet = (targetInput) => {
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
        let pass = "";
        for (let i = 0; i < 10; i++) {
            pass += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        targetInput.value = pass;
        const defaultInput = document.getElementById('defaultPasswordInput');
        if (defaultInput) defaultInput.value = pass;
    };

    if (generateBtnAdd && manualPassInput) {
        generateBtnAdd.addEventListener('click', () => generateAndSet(manualPassInput));
    }
    if (generateBtnEdit && editPassInput_gen) {
        generateBtnEdit.addEventListener('click', () => generateAndSet(editPassInput_gen));
    }


    // ── MODAL CLOSE CLEANUP ──
    const userModalEl = document.getElementById('userModal');
    if (userModalEl) {
        userModalEl.addEventListener('hidden.bs.modal', function () {
            const form = document.getElementById('userForm');
            if (form) form.reset();
            currentEditUserId = null;

            updateFieldsVisibility();

            document.getElementById('passwordAddSection').style.display = 'block';
            document.getElementById('passwordEditSection').style.display = 'none';
            if (useDefaultCb) useDefaultCb.checked = true;
            if (manualPasswordField) manualPasswordField.style.display = 'none';
            if (defaultPasswordInput) defaultPasswordInput.value = 'STAFFKTU123';
            if (manualPasswordInput) manualPasswordInput.value = '';
            if (changePasswordCb) changePasswordCb.checked = false;
            if (editPasswordField) editPasswordField.style.display = 'none';
            if (editPasswordInput) editPasswordInput.value = '';

            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
    }

    console.log('✅ Admin.js loaded successfully');
});

// Make functions globally accessible (just in case)
window.openUserModal = openUserModal;
window.closeUserModal = closeUserModal;
window.editUser = editUser;
window.deleteUser = deleteUser;
window.toggleSidebar = toggleSidebar;