{{-- ===========================
✨ MODERN USER MODAL (Add/Edit User)
=========================== --}}
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

      {{-- HEADER --}}
      <div class="modal-header bg-primary text-white py-3 px-4">
        <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="modalTitle">
          <i class="fas fa-user-edit"></i>
          <span id="userModalTitle">Add User</span>
        </h5>
        
      </div>

      {{-- FORM --}}
      <form id="userForm" action="{{ route('admin.users.store') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <input type="hidden" id="user_id" name="user_id">

        {{-- BODY --}}
        <div class="modal-body bg-light p-4">

          {{-- 🧍 NAME & STAFF ID --}}
          <div class="row g-4 mb-3">
            <div class="col-md-6">
              <label for="name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
              <input type="text" id="name" name="name" class="form-control form-control-lg shadow-sm"
                     placeholder="Enter full name" required>
            </div>
            <div class="col-md-6">
              <label for="id_staff" class="form-label fw-semibold">Staff ID <span class="text-danger">*</span></label>
              <input type="text" id="id_staff" name="id_staff" class="form-control form-control-lg shadow-sm"
                     placeholder="e.g. ST1234" required>
            </div>
          </div>

          {{-- ✉️ EMAIL --}}
          <div class="mb-4">
            <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
            <input type="email" id="email" name="email" class="form-control form-control-lg shadow-sm"
                   placeholder="example@company.com" required>
          </div>

          {{-- 🧩 ROLE & DEPARTMENT --}}
          <div class="row g-4 mb-3">
            <div class="col-md-6">
              <label for="role" class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
              <select id="role" name="role" class="form-select form-select-lg shadow-sm" required>
                <option value="">Select Role</option>
                <option value="user">Staff</option>
                <option value="tim it">IT Team</option>
              </select>
            </div>

            <div class="col-md-6">
              <label for="department_id" class="form-label fw-semibold">Department <span class="text-danger">*</span></label>
              <select id="department_id" name="department_id" class="form-select form-select-lg shadow-sm" required>
                <option value="">Select Department</option>
                @isset($departments)
                  @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ strtoupper($dept->name) }}</option>
                  @endforeach
                @endisset
              </select>
            </div>
          </div>

          {{-- ℹ️ DEFAULT PASSWORD NOTICE --}}
          <div class="alert alert-info d-flex align-items-center py-2 mt-4 rounded-3 shadow-sm small mb-0">
            <i class="fas fa-info-circle fa-lg me-2 text-primary"></i>
            <div>
              Default password: <strong>STAFFKTU123</strong><br>
              <small class="text-muted">User can change password later.</small>
              <input type="hidden" name="password" value="STAFFKTU123">
            </div>
          </div>

        </div>

        {{-- FOOTER --}}
        <div class="modal-footer bg-white border-0 pt-3 pb-4 px-4 d-flex justify-content-end gap-2">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancel
          </button>
          <button type="submit" class="btn btn-primary px-4" id="saveUserBtn">
            <i class="fas fa-save me-1"></i> Save User
          </button>
        </div>

      </form>
    </div>
  </div>
</div>
