@extends('layouts.it')

@section('title', 'Manage Users')

@section('content')

<h3>User Management</h3>

<div>
    <button class="btn btn-primary" id="openAddModal">Add User</button>
</div>

<table class="table table-striped mt-3" id="userTable">
    <thead>
        <tr>
            <th>Name</th>
            <th>ID Staff</th>
            <th>Email</th>
            <th>Department</th>
            <th>Actions</th>
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
                <button class="btn btn-sm btn-primary editUser" data-id="{{ $user->id }}">Edit</button>
                <button class="btn btn-sm btn-danger deleteUser" data-id="{{ $user->id }}">Delete</button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="userForm">
                @csrf
                <input type="hidden" id="user_id" name="user_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- Container untuk menampilkan error validasi --}}
                    <div id="error-container" class="alert alert-danger d-none"></div>

                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ID Staff</label>
                        <input type="text" class="form-control" name="id_staff" id="id_staff" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <select name="department" id="department" class="form-control" required>
                            <option value="">-- Select Department --</option>
                            <option value="IT">IT</option>
                            <option value="Engineering">Engineering</option>
                            <option value="hse">HSE</option>
                            <option value="hr">HR</option>
                            <option value="finance">Finance</option>
                            <option value="production">Production</option>
                            <option value="project">Project</option>
                            <option value="security">Security</option>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        Default password: <strong>STAFFKTU123</strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Save User</button>
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
    const userTableBody = document.getElementById('userTable').querySelector('tbody');
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const errorContainer = document.getElementById('error-container');

    // --- FUNGSI ---
    function openModal(title) {
        document.getElementById('modalTitle').innerText = title;
        errorContainer.classList.add('d-none'); // Sembunyikan error saat modal dibuka
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
                <button class="btn btn-sm btn-primary editUser" data-id="${data.id}">Edit</button>
                <button class="btn btn-sm btn-danger deleteUser" data-id="${data.id}">Delete</button>
            </td>`;

        const existingRow = document.getElementById(`row-${data.id}`);
        if (existingRow) { // Jika update, ganti isi row yang ada
            existingRow.innerHTML = rowHtml;
        } else { // Jika add, buat row baru
            const newRow = userTableBody.insertRow();
            newRow.id = `row-${data.id}`;
            newRow.innerHTML = rowHtml;
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

    // 1. Tombol "Add User" diklik
    document.getElementById('openAddModal').addEventListener('click', () => {
        userForm.reset();
        document.getElementById('user_id').value = '';
        openModal('Add User');
    });

    // 2. Form di-submit (untuk Add atau Edit)
    userForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const id = document.getElementById('user_id').value;
        const formData = new FormData(userForm);
        const url = id ? `/it/staff/${id}` : "{{ route('it.staff.store') }}";
        
        // Tambahkan _method spoofing untuk request PUT
        if (id) {
            formData.append('_method', 'PUT');
        }

        try {
            const response = await fetch(url, {
                method: 'POST', // Selalu POST untuk form, Laravel akan handle _method
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (!response.ok) {
                // Jika response error (misal: validasi gagal 422)
                if (response.status === 422) {
                    displayErrors(data.errors);
                }
                throw new Error(data.message || 'An error occurred.');
            }

            updateTableRow(data); // Panggil fungsi untuk update tabel
            alert(id ? 'User updated successfully!' : 'User added successfully!');
            closeModal();

        } catch (error) {
            console.error('Submit Error:', error);
            // Error akan ditampilkan di dalam modal oleh fungsi displayErrors
        }
    });

    // 3. Tombol "Edit" atau "Delete" di dalam tabel diklik
    userTableBody.addEventListener('click', async function (e) {
        const target = e.target;
        const id = target.dataset.id;

        if (!id) return; // Abaikan jika yang diklik bukan tombol dengan data-id

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

});
</script>
@endpush