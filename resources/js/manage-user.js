document.addEventListener('DOMContentLoaded', function () {
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    if (!tokenMeta) console.warn('CSRF meta tag not found!');
    const token = tokenMeta ? tokenMeta.getAttribute('content') : '';

    const userModalEl = document.getElementById('userModal');
    const userModal = new bootstrap.Modal(userModalEl);
    const userForm = document.getElementById('userForm');
    const userTableBody = document.querySelector('#userTable tbody');
    const errorContainer = document.getElementById('error-container');
    const searchInput = document.getElementById('searchInput');

    const deptModalEl = document.getElementById('deptModal');
    const deptModal = new bootstrap.Modal(deptModalEl);
    const deptForm = document.getElementById('deptForm');
    const deptErrorContainer = document.getElementById('dept-error-container');

    function openUserModal(title) {
        document.getElementById('modalTitle').innerText = title;
        errorContainer.classList.add('d-none');
        userForm.classList.remove('was-validated');
        userModal.show();
    }

    function closeUserModal() {
        if (userModal) userModal.hide();
        if (userForm) {
            userForm.reset();
            userForm.classList.remove('was-validated');
        }

        const setVal = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.value = val;
        };

        const toggleClass = (id, className, add = true) => {
            const el = document.getElementById(id);
            if (el) {
                if (add) el.classList.add(className);
                else el.classList.remove(className);
            }
        };

        setVal('user_id', '');
        if (errorContainer) errorContainer.classList.add('d-none');

        setVal('password', 'STAFFKTU123');
        toggleClass('passwordAddSection', 'd-none', false);
        toggleClass('passwordEditSection', 'd-none', true);

        const cb = document.getElementById('changePasswordToggle');
        if (cb) cb.checked = false;

        toggleClass('editPasswordField', 'd-none', true);
        setVal('editPassword', '');
    }

    function updateTableRow(data) {
        const rowHtml = `
            <td>${data.name}</td>
            <td>${data.nik}</td>
            <td>${data.username || '-'}</td>
            <td>${data.email}</td>
            <td>${data.department_name}</td>
            <td>
                <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-sm btn-primary editUser" data-id="${data.id}">
                        <i class="fas fa-edit"></i><span class="d-none d-md-inline ms-1">Edit</span>
                    </button>
                    <button class="btn btn-sm btn-danger deleteUser" data-id="${data.id}">
                        <i class="fas fa-trash-alt"></i><span class="d-none d-md-inline ms-1">Delete</span>
                    </button>
                </div>
            </td>`;
        const existingRow = document.getElementById(`row-${data.id}`);
        if (existingRow) {
            existingRow.innerHTML = rowHtml;
        } else {
            const newRow = document.createElement('tr');
            newRow.id = `row-${data.id}`;
            newRow.innerHTML = rowHtml;
            userTableBody.appendChild(newRow);
        }
    }

    function displayErrors(errors) {
        let html = '<ul class="mb-0">';
        for (const err in errors) {
            errors[err].forEach(msg => {
                html += `<li>${msg}</li>`;
            });
        }
        html += '</ul>';
        errorContainer.innerHTML = html;
        errorContainer.classList.remove('d-none');
    }

    // --- Add User ---
    const openAddBtn = document.getElementById('openAddModal');
    if (openAddBtn) {
        openAddBtn.addEventListener('click', () => {
            if (userForm) userForm.reset();

            const setVal = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.value = val;
            };

            const toggleClass = (id, className, add = true) => {
                const el = document.getElementById(id);
                if (el) {
                    if (add) el.classList.add(className);
                    else el.classList.remove(className);
                }
            };

            setVal('password', 'STAFFKTU123');
            toggleClass('passwordAddSection', 'd-none', false);
            toggleClass('passwordEditSection', 'd-none', true);
            openUserModal('Add User');
        });
    }

    if (userForm) {
        userForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            e.stopPropagation();

            // ✅ Bootstrap validation
            if (!userForm.checkValidity()) {
                userForm.classList.add('was-validated');
                return;
            }

            const userIdEl = document.getElementById('user_id');
            const id = userIdEl ? userIdEl.value : '';
            const formData = new FormData(userForm);
            // Use dataset for route
            const url = id ? `/it/staff/${id}` : userForm.dataset.storeRoute;

            if (id) formData.append('_method', 'PUT');

            // ✅ Untuk update, hanya kirim password jika toggle nyala
            if (id) {
                const changeToggle = document.getElementById('changePasswordToggle');
                const editPass = document.getElementById('editPassword').value;
                if (changeToggle && changeToggle.checked && editPass) {
                    formData.append('password', editPass);
                } else {
                    formData.delete('password');
                }
            }

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422) {
                        displayErrors(data.errors);
                    }
                    throw new Error(data.message || 'Error saving user');
                }

                updateTableRow(data);

                alert(id ? 'User updated successfully!' : 'User added successfully with default password: STAFFKTU123');
                closeUserModal();

            } catch (e) {
                console.error('User Submit Error:', e);
            }
        });
    }

    // --- Edit/Delete User ---
    if (userTableBody) {
        userTableBody.addEventListener('click', async function (e) {
            const btn = e.target.closest('button');
            if (!btn || !btn.dataset.id) return;
            const id = btn.dataset.id;

            // ✅ EDIT USER
            if (btn.classList.contains('editUser')) {
                try {
                    const res = await fetch(`/it/staff/${id}`);
                    if (!res.ok) throw new Error('Failed to fetch user data');

                    const data = await res.json();

                    openUserModal('Edit User');

                    // Safe value setter helper
                    const setVal = (id, val) => {
                        const el = document.getElementById(id);
                        if (el) el.value = val !== null && val !== undefined ? val : '';
                    };

                    // Safe class/display toggle helper
                    const toggleClass = (id, className, add = true) => {
                        const el = document.getElementById(id);
                        if (el) {
                            if (add) el.classList.add(className);
                            else el.classList.remove(className);
                        }
                    };

                    setVal('user_id', data.id);
                    setVal('name', data.name);
                    setVal('nik', data.nik);
                    setVal('username', data.username);
                    setVal('email', data.email);
                    setVal('department_id', data.department_id);
                    setVal('location_id', data.location_id);

                    // ✅ Password logic for edit
                    toggleClass('passwordAddSection', 'd-none', true);
                    toggleClass('passwordEditSection', 'd-none', false);

                    const cb = document.getElementById('changePasswordToggle');
                    if (cb) cb.checked = false;

                    toggleClass('editPasswordField', 'd-none', true);
                    setVal('editPassword', '');

                } catch (err) {
                    console.error('Edit fetch error:', err);
                    alert('Error: ' + err.message);
                }
            }

            // ✅ DELETE USER
            if (btn.classList.contains('deleteUser')) {
                if (confirm('Are you sure you want to delete this user?')) {
                    try {
                        const res = await fetch(`/it/staff/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await res.json();

                        if (!res.ok) {
                            throw new Error(data.message || 'Delete failed');
                        }

                        document.getElementById(`row-${id}`).remove();
                        alert('User deleted successfully!');

                    } catch (err) {
                        alert('Error: ' + err.message);
                    }
                }
            }
        });
    }

    // --- Search ---
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(this.searchTimeout);
            const query = this.value.trim();

            this.searchTimeout = setTimeout(async () => {
                try {
                    const res = await fetch(`/it/staff?search=${encodeURIComponent(query)}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    if (!res.ok) throw new Error('Search failed');

                    const data = await res.json();
                    userTableBody.innerHTML = '';

                    if (data.length === 0) {
                        userTableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No users found.</td></tr>';
                    } else {
                        data.forEach(u => updateTableRow(u));
                    }

                } catch (e) {
                    console.error('Search Error:', e);
                }
            }, 300);
        });
    }

    // --- Add Department ---
    const openDeptBtn = document.getElementById('openDeptModal');
    if (openDeptBtn) {
        openDeptBtn.addEventListener('click', () => {
            deptForm.reset();
            deptErrorContainer.classList.add('d-none');
            deptModal.show();
        });
    }

    if (deptForm) {
        deptForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const deptName = document.getElementById('dept_name').value.trim();
            if (!deptName) return;

            try {
                const response = await fetch("/it/departments", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name: deptName })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to add department');
                }

                // ✅ Update dropdown select
                const select = document.getElementById('department_id');
                const option = document.createElement('option');
                option.value = data.id;
                option.text = data.name.toUpperCase();
                option.selected = true;
                select.appendChild(option);

                deptModal.hide();
                alert('Department added successfully!');

            } catch (err) {
                console.error('Dept Error:', err);
                deptErrorContainer.innerHTML = `<p class="mb-0">${err.message}</p>`;
                deptErrorContainer.classList.remove('d-none');
            }
        });
    }

    // --- Password Toggles & Logic ---
    const changeToggle = document.getElementById('changePasswordToggle');
    const editPasswordField = document.getElementById('editPasswordField');
    const showEditPass = document.getElementById('showEditPassword');
    const editPassInput = document.getElementById('editPassword');

    if (changeToggle) {
        changeToggle.addEventListener('change', function () {
            if (this.checked) {
                editPasswordField.classList.remove('d-none');
                editPassInput.focus();
            } else {
                editPasswordField.classList.add('d-none');
                editPassInput.value = '';
            }
        });
    }

    if (showEditPass && editPassInput) {
        showEditPass.addEventListener('change', function () {
            editPassInput.type = this.checked ? 'text' : 'password';
        });
    }

    const genPassBtn = document.getElementById('generatePassword');
    if (genPassBtn) {
        genPassBtn.addEventListener('click', () => {
            const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
            let pass = "";
            for (let i = 0; i < 10; i++) {
                pass += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('password').value = pass;
        });
    }

});
