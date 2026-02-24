@extends('layouts.it')

@section('title', 'Manage Users')


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
                            <th>NIK</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr id="row-{{ $user->id }}">
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->nik }}</td>
                            <td>{{ $user->username ?? '-' }}</td>
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

            <form id="userForm" class="needs-validation p-3" novalidate data-store-route="{{ route('it.staff.store') }}">
                <input type="hidden" id="user_id" name="user_id">
                <div class="modal-body">
                    <div id="error-container" class="alert alert-danger d-none"></div>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="name" class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" required>
                            <div class="invalid-feedback">Name is required</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="nik" class="form-label fw-semibold">NIK <span class="text-muted small">(Optional)</span></label>
                            <input type="text" id="nik" name="nik" class="form-control">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="username" class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                            <input type="text" id="username" name="username" class="form-control" required>
                            <div class="invalid-feedback">Username is required</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" class="form-control" required>
                            <div class="invalid-feedback">Valid email is required</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="department_id" class="form-label fw-semibold">Department <span class="text-danger">*</span></label>
                            <select id="department_id" name="department_id" class="form-select" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ strtoupper($dept->name) }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Department is required</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="location_id" class="form-label fw-semibold">Location <span class="text-danger">*</span></label>
                            <select id="location_id" name="location_id" class="form-select" required>
                                <option value="">Select Location</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Location is required</div>
                        </div>

                        <!-- ✅ Password Section -->
                        <div class="col-12">
                            <div id="passwordAddSection">
                                <label for="password" class="form-label fw-semibold">Password</label>
                                <div class="input-group">
                                    <input type="text" id="password" name="password" class="form-control" value="STAFFKTU123" autocomplete="new-password">
                                    <button class="btn btn-outline-secondary" type="button" id="generatePassword">
                                        <i class="fas fa-magic"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Default: <strong class="text-primary">STAFFKTU123</strong></small>
                            </div>

                            <div id="passwordEditSection" class="d-none">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="changePasswordToggle">
                                    <label class="form-check-label fw-semibold" for="changePasswordToggle">Change Password</label>
                                </div>
                                <div id="editPasswordField" class="d-none">
                                    <input type="password" id="editPassword" name="edit_password" class="form-control" placeholder="Enter new password" autocomplete="new-password">
                                    <div class="form-check mt-1">
                                        <input class="form-check-input" type="checkbox" id="showEditPassword">
                                        <label class="form-check-label small text-muted" for="showEditPassword">Show password</label>
                                    </div>
                                </div>
                            </div>
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

@push('styles')
    @vite('resources/css/manage-user.css')
@endpush

@push('scripts')
    @vite('resources/js/manage-user.js')
@endpush