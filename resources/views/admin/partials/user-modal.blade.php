{{-- ===========================
✨ MODERN USER MODAL (Floating Labels)
=========================== --}}
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

      {{-- HEADER --}}
      <div class="modal-header bg-gradient-primary text-white py-3 px-4" style="background: linear-gradient(45deg, #4e73df, #224abe);">
        <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="modalTitle">
          <i class="fas fa-user-circle fa-lg"></i>
          <span id="userModalTitle">Manage User</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      {{-- FORM --}}
      <form id="userForm" action="{{ route('admin.users.store') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <input type="hidden" id="user_id" name="user_id">

        {{-- BODY --}}
        <div class="modal-body p-4 bg-white" style="max-height: 70vh; overflow-y: auto;">
          
          <p class="text-muted small mb-4"><i class="fas fa-info-circle me-1"></i> Please fill in the details below to manage the user account.</p>

          {{-- SECTION 1: PERSONAL DETAILS --}}
          <h6 class="fw-bold text-secondary text-uppercase small ls-1 mb-3">Personal Details</h6>
          <div class="row g-3 mb-4">
            {{-- Name --}}
            <div class="col-md-7">
              <div class="form-floating">
                <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" required>
                <label for="name"><i class="fas fa-user me-2 text-muted"></i>Full Name <span class="text-danger">*</span></label>
              </div>
            </div>

            {{-- Staff ID --}}
            <div class="col-md-5">
              <div class="form-floating">
                <input type="text" class="form-control" id="id_staff" name="id_staff" placeholder="Staff ID" autocomplete="username" required>
                <label for="id_staff"><i class="fas fa-id-badge me-2 text-muted"></i>Staff ID / NIK <span class="text-danger">*</span></label>
              </div>
            </div>

            {{-- Email --}}
            <div class="col-12">
              <div class="form-floating">
                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" autocomplete="email">
                <label for="email"><i class="fas fa-envelope me-2 text-muted"></i>Email Address <span class="text-muted small">(Optional)</span></label>
              </div>
              {{-- Info specifically for IT Team --}}
              <div id="emailItNote" class="alert alert-warning py-2 px-3 mt-2 small mb-0" style="display:none;">
                <i class="fas fa-exclamation-triangle me-1"></i>
                <strong>Required for IT Team</strong> — this email is used to receive ticket notifications from the same region.
              </div>
            </div>
          </div>

          {{-- SECTION 2: SYSTEM ACCESS --}}
          <h6 class="fw-bold text-secondary text-uppercase small ls-1 mb-3 pt-2 border-top">System Access</h6>
          <div class="row g-3">
            {{-- Department --}}
            <div class="col-md-6">
              <div class="form-floating">
                <select class="form-select" id="department_id" name="department_id" required>
                  <option value="" selected disabled>Select Department</option>
                  @isset($departments)
                    @foreach($departments as $dept)
                      <option value="{{ $dept->id }}">{{ strtoupper($dept->name) }}</option>
                    @endforeach
                  @endisset
                </select>
                <label for="department_id"><i class="fas fa-building me-2 text-muted"></i>Department <span class="text-danger">*</span></label>
              </div>
            </div>

            {{-- Role --}}
            <div class="col-md-6">
              <div class="form-floating">
                <select class="form-select" id="role" name="role" required>
                  <option value="" selected disabled>Select Role</option>
                  <option value="user">Staff / User</option>
                  <option value="tim it">IT Team (Admin)</option>
                </select>
                <label for="role"><i class="fas fa-user-tag me-2 text-muted"></i>System Role <span class="text-danger">*</span></label>
              </div>
            </div>

            {{-- Region --}}
            <div class="col-12" id="regionField">
              <div class="form-floating">
                <select class="form-select" id="region_id" name="region_id">
                    <option value="" selected disabled>Select Region</option>
                    @isset($regions)
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}">{{ $region->name }}</option>
                        @endforeach
                    @endisset
                </select>
                <label for="region_id"><i class="fas fa-globe me-2 text-muted"></i>Region <span class="text-danger">*</span></label>
              </div>
            </div>

            {{-- Location --}}
            <div class="col-12" id="locationField">
              <div class="form-floating">
                <select class="form-select" id="location_id" name="location_id">
                  <option value="" selected disabled>Select Location</option>
                  @isset($locations)
                    @foreach($locations as $loc)
                      <option value="{{ $loc->id }}" data-region-id="{{ $loc->region_id }}">{{ $loc->name }}</option>
                    @endforeach
                  @endisset
                </select>
                <label for="location_id"><i class="fas fa-map-marker-alt me-2 text-muted"></i>Location <span class="text-danger">*</span></label>
              </div>
            </div>
          </div>

          {{-- PASSWORD SECTION --}}
          <div class="mt-4 pt-3 border-top">
            <h6 class="fw-bold text-secondary text-uppercase small ls-1 mb-3">
              <i class="fas fa-lock me-2"></i>Password
            </h6>

            {{-- ADD MODE: checkbox to choose default or manual --}}
            <div id="passwordAddSection">
              <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="useDefaultPassword" checked>
                <label class="form-check-label fw-semibold" for="useDefaultPassword">
                  Use default password &nbsp;<code class="text-primary">STAFFKTU123</code>
                </label>
              </div>

              {{-- Manual password input (no name — value is copied to hidden input by JS) --}}
              <div id="manualPasswordField" style="display:none;">
                <div class="form-floating">
                  <input type="password" class="form-control" id="password"
                         placeholder="Enter password" autocomplete="new-password">
                  <label for="password"><i class="fas fa-key me-2 text-muted"></i>Password</label>
                </div>
                <div class="form-check mt-2">
                  <input class="form-check-input" type="checkbox" id="togglePasswordVisibility">
                  <label class="form-check-label small text-muted" for="togglePasswordVisibility">Show password</label>
                </div>
              </div>

              {{-- Single hidden input — always submitted with the final password value --}}
              <input type="hidden" id="defaultPasswordInput" name="password" value="STAFFKTU123">
            </div>

            {{-- EDIT MODE: checkbox to optionally change password --}}
            <div id="passwordEditSection" style="display:none;">
              <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="changePassword">
                <label class="form-check-label fw-semibold" for="changePassword">
                  Change password
                </label>
                <small class="d-block text-muted mt-1">Leave unchecked to keep the current password.</small>
              </div>

              <div id="editPasswordField" style="display:none;">
                <div class="form-floating">
                  {{-- No name here — JS copies value into hidden input on submit --}}
                  <input type="password" class="form-control" id="editPassword"
                         placeholder="New password" autocomplete="new-password">
                  <label for="editPassword"><i class="fas fa-key me-2 text-muted"></i>New Password</label>
                </div>
                <div class="form-check mt-2">
                  <input class="form-check-input" type="checkbox" id="toggleEditPasswordVisibility">
                  <label class="form-check-label small text-muted" for="toggleEditPasswordVisibility">Show password</label>
                </div>
              </div>
            </div>
          </div>


        </div>

        {{-- FOOTER --}}
        <div class="modal-footer bg-light px-4 py-3">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm" id="saveUserBtn">
            <i class="fas fa-save me-2"></i> Save Changes
          </button>
        </div>

      </form>
    </div>
  </div>
</div>
