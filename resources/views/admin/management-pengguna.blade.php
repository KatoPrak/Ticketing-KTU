@extends('layouts.admin')

@section('title', 'User Management')

@section('content')

<!-- Display Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert">&times;</button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert">&times;</button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Please check the following errors:</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert">&times;</button>
    </div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">User Management</h3>
        <div class="d-flex gap-3">
            <!-- Search Input -->
            <div class="search-container">
                <input type="text" id="searchInput" class="form-control" placeholder="Search by Staff ID..." style="width: 250px;">
            </div>
            <button class="btn btn-primary open-user-modal">
                <i class="fas fa-plus"></i> Add User
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table id="usersTable" class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>ID Staff</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Department</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($users) && $users->count() > 0)
                    @foreach($users as $user)
                        @if($user->role !== 'admin')
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td class="staff-id">{{ $user->id_staff }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ ucfirst($user->role) }}</td>
                                <td><span class="badge badge-success">{{ strtoupper($user->department) }}</span></td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-user" data-id="{{ $user->id }}">Edit</button>
                                    <button class="btn btn-sm btn-danger delete-user" data-id="{{ $user->id }}">Delete</button>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @else
                    <tr id="noUsersRow">
                        <td colspan="6" class="text-center">No users found</td>
                    </tr>
                @endif
                <tr id="noResultsRow" style="display: none;">
                    <td colspan="6" class="text-center">No users found matching your search</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div id="userModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">TAMBAHKAN PENGGUNA</h3>
            <span class="close close-user-modal">&times;</span>
        </div>
        <div class="modal-body">
            <form id="userForm" action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <input type="hidden" id="user_id" name="user_id" value="">
                
                <div class="form-group">
                    <label class="form-label">Name *</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">ID Staff *</label>
                    <input type="text" class="form-control" id="id_staff" name="id_staff" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Role *</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="user">Staff</option>
                            <option value="tim it">IT Team</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Department *</label>
                        <select class="form-control" id="department" name="department" required>
                            <option value="">Select Department</option>
                            <option value="hse">HSE</option>
                            <option value="hr">HR</option>
                            <option value="finance">Finance</option>
                            <option value="production">Production</option>
                            <option value="project">Project</option>
                            <option value="security">Security</option>
                            <option value="commercial">Commercial</option>
                            <option value="purchasing">Purchasing</option>
                            <option value="account-payable">Account Payable</option>
                            <option value="engineering">Engineering</option>
                            <option value="qc">Quality Control</option>
                            <option value="store">Store</option>
                            <option value="accounting">Accounting</option>
                            <option value="inventory">Inventory</option>
                            <option value="admin-project">Admin Project</option>
                            <option value="iso">ISO</option>
                            <option value="ppic">PPIC</option>
                            <option value="planner">Planner</option>
                            <option value="facility">Facility</option>
                            <option value="outfitting">Outfitting</option>
                        </select>
                    </div>
                </div>

                <div class="alert alert-info">
                    <small><i class="fas fa-info-circle"></i> Default password: <strong>STAFFKTU123</strong></small>
                </div>

                <input type="hidden" name="password" value="STAFFKTU123">

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-user-modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 0;
    border: none;
    width: 90%;
    max-width: 600px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.modal-header {
    padding: 20px;
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    border-radius: 8px 8px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 1.25rem;
    color: #495057;
}

.close {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 1;
    background: none;
    border: none;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
}

.modal-body {
    padding: 20px;
}

.form-group {
    margin-bottom: 1rem;
}

.form-row {
    display: flex;
    gap: 15px;
}

.form-row .form-group {
    flex: 1;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #495057;
}

.form-control {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    font-size: 1rem;
    line-height: 1.5;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.modal-footer {
    padding: 15px 20px;
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
    border-radius: 0 0 8px 8px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 0.25rem;
    cursor: pointer;
    font-size: 0.9rem;
    text-decoration: none;
    display: inline-block;
    font-weight: 400;
    text-align: center;
    vertical-align: middle;
}

.btn-primary {
    background-color: #007bff;
    color: #fff;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-secondary {
    background-color: #6c757d;
    color: #fff;
}

.btn-secondary:hover {
    background-color: #545b62;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}

.btn-danger {
    background-color: #dc3545;
    color: #fff;
}

.btn-danger:hover {
    background-color: #c82333;
}

.alert {
    padding: 0.75rem 1.25rem;
    margin-bottom: 1rem;
    border: 1px solid transparent;
    border-radius: 0.25rem;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.alert-info {
    color: #0c5460;
    background-color: #d1ecf1;
    border-color: #bee5eb;
}

.badge {
    display: inline-block;
    padding: 0.25em 0.4em;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.25rem;
}

.badge-success {
    color: #fff;
    background-color: #28a745;
}

.table {
    width: 100%;
    margin-bottom: 1rem;
    color: #212529;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0, 0, 0, 0.05);
}

.table th,
.table td {
    padding: 0.75rem;
    vertical-align: top;
    border-top: 1px solid #dee2e6;
}

.table thead th {
    vertical-align: bottom;
    border-bottom: 2px solid #dee2e6;
}

.card {
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid rgba(0, 0, 0, 0.125);
    border-radius: 0.25rem;
}

.card-header {
    padding: 0.75rem 1.25rem;
    margin-bottom: 0;
    background-color: rgba(0, 0, 0, 0.03);
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.d-flex {
    display: flex !important;
}

.justify-content-between {
    justify-content: space-between !important;
}

.align-items-center {
    align-items: center !important;
}

.text-center {
    text-align: center !important;
}

.btn-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    opacity: 0.5;
}

.btn-close:hover {
    opacity: 1;
}

.gap-3 {
    gap: 1rem;
}

.search-container {
    display: flex;
    align-items: center;
}

.search-container .form-control {
    height: 38px;
    border-radius: 4px;
}

/* Highlight search results */
.highlight {
    background-color: #ffeb3b;
    padding: 1px 2px;
    border-radius: 2px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal elements
    const modal = document.getElementById('userModal');
    const openModalBtn = document.querySelector('.open-user-modal');
    const closeModalBtns = document.querySelectorAll('.close-user-modal');
    const userForm = document.getElementById('userForm');

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('#usersTable tbody');
    const allRows = tableBody.querySelectorAll('tr:not(#noUsersRow):not(#noResultsRow)');
    const noResultsRow = document.getElementById('noResultsRow');

    // Search function
    function performSearch(searchTerm) {
        searchTerm = searchTerm.toLowerCase().trim();
        let visibleRowCount = 0;

        allRows.forEach(row => {
            const staffIdCell = row.querySelector('.staff-id');
            if (staffIdCell) {
                const staffId = staffIdCell.textContent.toLowerCase();
                
                // Clear previous highlights
                staffIdCell.innerHTML = staffIdCell.textContent;
                
                if (searchTerm === '' || staffId.includes(searchTerm)) {
                    row.style.display = '';
                    visibleRowCount++;
                    
                    // Highlight matching text
                    if (searchTerm !== '' && staffId.includes(searchTerm)) {
                        const originalText = staffIdCell.textContent;
                        const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                        const highlightedText = originalText.replace(regex, '<span class="highlight">$1</span>');
                        staffIdCell.innerHTML = highlightedText;
                    }
                } else {
                    row.style.display = 'none';
                }
            }
        });

        // Show/hide "no results" message
        if (visibleRowCount === 0 && searchTerm !== '') {
            noResultsRow.style.display = '';
        } else {
            noResultsRow.style.display = 'none';
        }
    }

    // Search input event listener
    searchInput.addEventListener('input', function() {
        performSearch(this.value);
    });

    // Clear search when input is cleared
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            performSearch('');
            this.blur();
        }
    });

    // Open modal untuk add user
    openModalBtn.addEventListener('click', function() {
        document.getElementById('modalTitle').textContent = 'TAMBAHKAN PENGGUNA';
        userForm.reset();
        document.getElementById('user_id').value = '';
        userForm.action = "{{ route('admin.users.store') }}";
        
        // Remove method field if exists
        const methodField = userForm.querySelector('input[name="_method"]');
        if (methodField) {
            methodField.remove();
        }
        
        modal.style.display = 'block';
    });

    // Close modal
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    });

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    // Edit user functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-user')) {
            const userId = e.target.getAttribute('data-id');
            
            // Get user data via AJAX
            fetch(`/admin/users/${userId}`)
                .then(response => response.json())
                .then(user => {
                    document.getElementById('modalTitle').textContent = 'EDIT PENGGUNA';
                    document.getElementById('user_id').value = user.id;
                    document.getElementById('name').value = user.name;
                    document.getElementById('id_staff').value = user.id_staff;
                    document.getElementById('email').value = user.email;
                    document.getElementById('role').value = user.role;
                    document.getElementById('department').value = user.department;
                    
                    // Change form action and method for update
                    userForm.action = `/admin/users/${userId}`;
                    let methodField = userForm.querySelector('input[name="_method"]');
                    if (!methodField) {
                        methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        userForm.appendChild(methodField);
                    }
                    methodField.value = 'PUT';
                    
                    modal.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading user data');
                });
        }
    });

    // Delete user functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-user')) {
            const userId = e.target.getAttribute('data-id');
            const userName = e.target.closest('tr').querySelector('td:first-child').textContent;
            
            if (confirm(`Are you sure you want to delete user "${userName}"?`)) {
                fetch(`/admin/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting user');
                });
            }
        }
    });

    // Form validation before submit
    userForm.addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const id_staff = document.getElementById('id_staff').value.trim();
        const email = document.getElementById('email').value.trim();
        const role = document.getElementById('role').value;
        const department = document.getElementById('department').value;

        if (!name || !id_staff || !email || !role || !department) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return false;
        }

        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Please enter a valid email address');
            return false;
        }

        // Show loading state
        const submitBtn = userForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        submitBtn.disabled = true;

        // Re-enable button after form submission
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 3000);
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 10000);
        });
    }, 10000);
});
</script>

@endsection