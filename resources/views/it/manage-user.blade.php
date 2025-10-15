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

    <!-- User Table -->
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
                            <td>{{ $user->department->name ?? '-' }}</td>
                            <td>
                                <div class="d-flex justify-content-center align-items-center flex-nowrap flex-md-wrap gap-2">
                                    <button class="btn btn-sm btn-primary editUser" data-id="{{ $user->id }}">
                                        <i class="fas fa-edit"></i><span class="d-none d-md-inline ms-1">Edit</span>
                                    </button>
                                    <button class="btn btn-sm btn-danger deleteUser" data-id="{{ $user->id }}">
                                        <i class="fas fa-trash-alt"></i><span class="d-none d-md-inline ms-1">Delete</span>
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

<!-- Modal User -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md modal-dialog-scrollable">
        <div class="modal-content rounded-3">
            <div class="modal-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="modal-title" id="modalTitle">Add User</h5>
                <div class="d-flex align-items-center">
                    <button type="button" id="openDeptModal" class="btn btn-sm btn-light text-dark me-2" title="Add Department">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
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
                            <label for="department_id" class="form-label fw-semibold">Department</label>
  <select id="department_id" name="department_id" class="form-select form-select-lg shadow-sm" required>
    <option value="">Select Department</option>
    @foreach($departments as $dept)
        <option value="{{ $dept->id }}">{{ strtoupper($dept->name) }}</option>
    @endforeach
</select>


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

<!-- Modal Add Department -->
<div class="modal fade" id="deptModal" tabindex="-1" aria-labelledby="deptModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-3">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="deptModalTitle">Add Department</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="deptForm" class="p-3">
                <div id="dept-error-container" class="alert alert-danger d-none"></div>
                <div class="mb-3">
                    <label for="dept_name" class="form-label fw-semibold">Department Name</label>
                    <input type="text" id="dept_name" name="dept_name" class="form-control" required>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Department</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    if(!tokenMeta) console.warn('CSRF meta tag not found! Please add <meta name="csrf-token" content=\"{{ csrf_token() }}\"> to your layout.');
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
        userModal.show();
    }
    function closeUserModal() {
        userModal.hide();
        userForm.reset();
        document.getElementById('user_id').value = '';
    }

    function updateTableRow(data) {
        const rowHtml = `
            <td>${data.name}</td>
            <td>${data.id_staff}</td>
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
        if(existingRow) existingRow.innerHTML = rowHtml;
        else {
            const newRow = document.createElement('tr');
            newRow.id = `row-${data.id}`;
            newRow.innerHTML = rowHtml;
            userTableBody.appendChild(newRow);
        }
    }

    function displayErrors(errors) {
        let html = '<ul>';
        for (const err in errors) html += `<li>${errors[err][0]}</li>`;
        html += '</ul>';
        errorContainer.innerHTML = html;
        errorContainer.classList.remove('d-none');
    }

    // --- Add User ---
    document.getElementById('openAddModal').addEventListener('click', () => openUserModal('Add User'));

    userForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const id = document.getElementById('user_id').value;
        const formData = new FormData(userForm);
        const url = id ? `/it/staff/${id}` : "{{ route('it.staff.store') }}";
        if(id) formData.append('_method', 'PUT');

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': token, 'Accept': 'application/json'},
                body: formData
            });
            const data = await response.json();
            if(!response.ok) { if(response.status===422) displayErrors(data.errors); throw new Error(data.message || 'Error'); }

            updateTableRow(data);
            alert(id ? 'User updated successfully!' : 'User added successfully!');
            closeUserModal();
        } catch(e) { console.error('User Submit Error:', e); }
    });

    // --- Edit/Delete User ---
    userTableBody.addEventListener('click', async function(e){
        const btn = e.target.closest('button');
        if(!btn || !btn.dataset.id) return;
        const id = btn.dataset.id;

        if(btn.classList.contains('editUser')){
            try{
                const res = await fetch(`/it/staff/${id}`);
                if(!res.ok) throw new Error('Fetch error');
                const data = await res.json();
                openUserModal('Edit User');
                document.getElementById('user_id').value = data.id;
                document.getElementById('name').value = data.name;
                document.getElementById('id_staff').value = data.id_staff;
                document.getElementById('email').value = data.email;
                document.getElementById('department_id').value = data.department_id;
            }catch(err){ alert(err.message); }
        }

        if(btn.classList.contains('deleteUser')){
            if(confirm('Are you sure to delete this user?')){
                try{
                    const res = await fetch(`/it/staff/${id}`, {
                        method:'DELETE',
                        headers:{ 'X-CSRF-TOKEN': token, 'Accept':'application/json' }
                    });
                    if(!res.ok) throw new Error('Delete failed');
                    document.getElementById(`row-${id}`).remove();
                    alert('User deleted successfully!');
                }catch(err){ alert(err.message); }
            }
        }
    });

    // --- Search ---
    searchInput.addEventListener('keyup', function(){
        clearTimeout(this.searchTimeout);
        const query = this.value.trim();
        this.searchTimeout = setTimeout(async ()=>{
            try{
                const res = await fetch(`/it/staff?search=${encodeURIComponent(query)}`, {headers:{'X-Requested-With':'XMLHttpRequest'}});
                if(!res.ok) throw new Error('Search failed');
                const data = await res.json();
                userTableBody.innerHTML='';
                if(data.length===0) userTableBody.innerHTML='<tr><td colspan="5" class="text-center text-muted">No users found.</td></tr>';
                data.forEach(u=>updateTableRow(u));
            }catch(e){ console.error('Search Error:', e); }
        },300);
    });

    // --- Add Department ---
    document.getElementById('openDeptModal').addEventListener('click', ()=>{
        deptForm.reset();
        deptErrorContainer.classList.add('d-none');
        deptModal.show();
    });

    deptForm.addEventListener('submit', async (e)=>{
        e.preventDefault();
        const deptName = document.getElementById('dept_name').value.trim();
        if(!deptName) return;

        try{
            const response = await fetch("/it/departments", {
                method:'POST',
                headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN':token, 'Accept':'application/json' },
                body: JSON.stringify({name:deptName})
            });

            const data = await response.json();
            if(!response.ok) throw new Error(data.message || 'Dept add error');

            // Update User select dropdown
            const select = document.getElementById('department_id');
            const option = document.createElement('option');
            option.value = data.id;
            option.text = data.name.toUpperCase();
            option.selected = true;
            select.appendChild(option);

            deptModal.hide();
            alert('Department added successfully!');
        }catch(err){ 
            console.error('Dept Error:', err);
            // jika validasi 422, tampilkan pesan
            if(err && err.message && err.message.includes('422')) {
                deptErrorContainer.innerHTML = 'Validation error';
                deptErrorContainer.classList.remove('d-none');
            }
        }
    });

});
</script>

<style>
.table td .btn i { font-size:16px; display:inline-block; color:#fff; }
.table-responsive{ overflow-x:auto; }
@media (max-width:768px){ .table td .btn span{display:none;} .table td .btn{ width:36px; height:36px; border-radius:50%; padding:0; justify-content:center; align-items:center; } }
@media (max-width:576px){ .table td .btn{ min-width:36px; padding:4px 6px; } .table td div.d-flex{ gap:0.4rem; } }
</style>
@endpush
