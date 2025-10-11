@extends('layouts.it')

@section('title', 'Manage User')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
        <h3 class="fw-bold text-dark mb-0"><i class="fas fa-users me-2"></i> Manage Users</h3>
        <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto">
            <input type="text" id="searchInput" class="form-control" placeholder="Search user..." aria-label="Search user">
            <button id="openAddModal" class="btn btn-primary w-100 w-sm-auto">
                <i class="fas fa-plus me-1"></i> Add User
            </button>
        </div>
    </div>

    <!-- Responsive Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-2 p-md-3">
            <div class="table-responsive">
                <table id="userTable" class="table table-hover align-middle text-center mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>ID Staff</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr id="row-{{ $user->id }}">
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->id_staff }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->department }}</td>
                            <td>
    <div class="d-flex justify-content-center align-items-center flex-nowrap flex-md-wrap gap-2">
        <button class="btn btn-sm btn-primary editUser d-flex justify-content-center align-items-center"
            data-id="{{ $user->id }}">
            <i class="fa-solid fa-pen-to-square fa-lg"></i>
            <span class="d-none d-md-inline ms-1">Edit</span>
        </button>
        <button class="btn btn-sm btn-danger deleteUser d-flex justify-content-center align-items-center"
            data-id="{{ $user->id }}">
            <i class="fa-solid fa-trash-can fa-lg"></i>
            <span class="d-none d-md-inline ms-1">Delete</span>
        </button>
    </div>
</td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md modal-dialog-scrollable">
        <div class="modal-content rounded-3">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Add User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="userForm" class="needs-validation p-3" novalidate>
                <input type="hidden" id="user_id" name="user_id">
                <div class="modal-body">
                    <div id="error-container" class="alert alert-danger d-none"></div>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="name" class="form-label fw-semibold">Name</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="id_staff" class="form-label fw-semibold">ID Staff</label>
                            <input type="text" id="id_staff" name="id_staff" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="department" class="form-label fw-semibold">Department</label>
                            <input type="text" id="department" name="department" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <input type="password" id="password" name="password" class="form-control">
                            <small class="text-muted">Leave blank to keep current password</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between flex-wrap">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // --- VARIABEL & ELEMENT ---
    const userModalEl = document.getElementById('userModal');
    const userModal = new bootstrap.Modal(userModalEl);
    const userForm = document.getElementById('userForm');
    const userTableBody = document.querySelector('#userTable tbody');
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const errorContainer = document.getElementById('error-container');
    const searchInput = document.getElementById('searchInput');

    // --- FUNGSI ---
    function openModal(title) {
        document.getElementById('modalTitle').innerText = title;
        errorContainer.classList.add('d-none');
        userModal.show();
    }

    function closeModal() {
        userModal.hide();
        userForm.reset();
        document.getElementById('user_id').value = '';
    }

    function updateTableRow(data) {
        const rowHtml = `
            <td>${data.name}</td>
            <td>${data.id_staff}</td>
            <td>${data.email}</td>
            <td>${data.department}</td>
            <td>
                <div class="d-flex justify-content-center flex-nowrap flex-md-wrap gap-2">
                    <button class="btn btn-sm btn-primary editUser d-flex align-items-center" data-id="${data.id}">
                        <i class="fas fa-edit me-1"></i><span class="d-none d-md-inline">Edit</span>
                    </button>
                    <button class="btn btn-sm btn-danger deleteUser d-flex align-items-center" data-id="${data.id}">
                        <i class="fas fa-trash-alt me-1"></i><span class="d-none d-md-inline">Delete</span>
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
        let errorHtml = '<ul>';
        for (const error in errors) {
            errorHtml += `<li>${errors[error][0]}</li>`;
        }
        errorHtml += '</ul>';
        errorContainer.innerHTML = errorHtml;
        errorContainer.classList.remove('d-none');
    }

    // --- EVENT LISTENERS ---

    // 1. Tombol "Add User"
    document.getElementById('openAddModal').addEventListener('click', () => {
        userForm.reset();
        document.getElementById('user_id').value = '';
        openModal('Add User');
    });

    // 2. Submit form (Add/Edit)
    userForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const id = document.getElementById('user_id').value;
        const formData = new FormData(userForm);
        const url = id ? `/it/staff/${id}` : "{{ route('it.staff.store') }}";

        if (id) formData.append('_method', 'PUT');

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (!response.ok) {
                if (response.status === 422) displayErrors(data.errors);
                throw new Error(data.message || 'An error occurred.');
            }

            updateTableRow(data);
            alert(id ? 'User updated successfully!' : 'User added successfully!');
            closeModal();

        } catch (error) {
            console.error('Submit Error:', error);
        }
    });

    // 3. Tombol Edit/Delete di tabel
    userTableBody.addEventListener('click', async function (e) {
        const target = e.target;
        const id = target.dataset.id;
        if (!id) return;

        // --- EDIT USER ---
        if (target.classList.contains('editUser')) {
            try {
                const response = await fetch(`/it/staff/${id}`);
                if (!response.ok) throw new Error('Failed to fetch user data.');
                const data = await response.json();

                openModal('Edit User');
                document.getElementById('user_id').value = data.id;
                document.getElementById('name').value = data.name;
                document.getElementById('id_staff').value = data.id_staff;
                document.getElementById('email').value = data.email;
                document.getElementById('department').value = data.department;

            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        // --- DELETE USER ---
        if (target.classList.contains('deleteUser')) {
            if (confirm('Are you sure you want to delete this user?')) {
                try {
                    const response = await fetch(`/it/staff/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                        }
                    });

                    if (!response.ok) throw new Error('Failed to delete user.');

                    document.getElementById(`row-${id}`).remove();
                    alert('User deleted successfully!');
                } catch (error) {
                    alert('Error: ' + error.message);
                }
            }
        }
    });

    // 4. --- SEARCH FEATURE (dipindah ke luar agar aktif sejak awal) ---
    let searchTimeout = null;

    searchInput.addEventListener('keyup', function () {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        searchTimeout = setTimeout(async () => {
            try {
                const response = await fetch(`/it/staff?search=${encodeURIComponent(query)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!response.ok) throw new Error('Search request failed.');
                const data = await response.json();

                userTableBody.innerHTML = '';

                if (data.length === 0) {
                    userTableBody.innerHTML = `<tr><td colspan="5" class="text-center text-muted">No users found.</td></tr>`;
                    return;
                }

                data.forEach(user => {
                    const row = document.createElement('tr');
                    row.id = `row-${user.id}`;
                    row.innerHTML = `
                        <td>${user.name}</td>
                        <td>${user.id_staff}</td>
                        <td>${user.email}</td>
                        <td>${user.department}</td>
                        <td>
                            <button class="btn btn-sm btn-primary editUser" data-id="${user.id}">Edit</button>
                            <button class="btn btn-sm btn-danger deleteUser" data-id="${user.id}">Delete</button>
                        </td>`;
                    userTableBody.appendChild(row);
                });

            } catch (error) {
                console.error('Search Error:', error);
            }
        }, 300);
    });

});
</script>
<style>
/* Pastikan ikon tidak hilang di layar kecil */
.table td .btn i {
    font-size: 14px;
}

/* Supaya tombol tidak terpotong di mobile */
.table-responsive {
    overflow-x: auto;
}
/* Pastikan ikon tidak hilang di layar kecil */
.table td .btn i {
    display: inline-block !important;
    font-size: 16px;
    color: #fff;
}

/* Hilangkan teks, tampilkan ikon saja di layar kecil */
@media (max-width: 768px) {
    .table td .btn span {
        display: none !important;
    }
    .table td .btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        padding: 0;
        justify-content: center;
        align-items: center;
    }
}

/* Tambahkan jarak dan kelonggaran antar tombol di layar kecil */
@media (max-width: 576px) {
    .table td .btn {
        min-width: 36px;
        padding: 4px 6px;
    }
    .table td div.d-flex {
        gap: 0.4rem;
    }
}
</style>
@endpush
