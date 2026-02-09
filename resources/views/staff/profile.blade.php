@extends('layouts.staff')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid mt-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="fas fa-user-circle me-2"></i>My Profile
            </h3>
            <p class="text-muted mb-0">Manage your account information</p>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        {{-- Profile Card --}}
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="profile-avatar mb-3">
                        <div class="avatar-circle">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-2">{{ $user->id_staff }}</p>
                    <span class="badge bg-primary">{{ ucfirst($user->role) }}</span>
                    
                    <hr class="my-3">
                    
                    <div class="profile-info text-start">
                        <div class="info-item mb-3">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            <small class="text-muted">Email</small>
                            <p class="mb-0 fw-semibold">{{ $user->email }}</p>
                        </div>
                        <div class="info-item mb-3">
                            <i class="fas fa-building text-primary me-2"></i>
                            <small class="text-muted">Department</small>
                            <p class="mb-0 fw-semibold">{{ $user->department->name ?? '-' }}</p>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                            <small class="text-muted">Location</small>
                            <p class="mb-0 fw-semibold">{{ $user->location->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Edit Form --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-edit me-2"></i>Edit Profile
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('staff.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Personal Information Section --}}
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fas fa-user me-2"></i>Personal Information
                        </h6>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $user->name) }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="id_staff" class="form-label fw-semibold">
                                    Staff ID
                                </label>
                                <input type="text" 
                                       class="form-control bg-light" 
                                       id="id_staff" 
                                       value="{{ $user->id_staff }}" 
                                       disabled 
                                       readonly>
                                <small class="text-muted">Staff ID cannot be changed</small>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email) }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="department_id" class="form-label fw-semibold">
                                    Department <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('department_id') is-invalid @enderror" 
                                        id="department_id" 
                                        name="department_id" 
                                        required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" 
                                                {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="location_id" class="form-label fw-semibold">
                                    Location <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('location_id') is-invalid @enderror" 
                                        id="location_id" 
                                        name="location_id" 
                                        required>
                                    <option value="">Select Location</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" 
                                                {{ old('location_id', $user->location_id) == $location->id ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('location_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Change Password Section --}}
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fas fa-lock me-2"></i>Change Password
                        </h6>
                        <p class="text-muted small mb-3">Leave blank if you don't want to change password</p>

                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label for="current_password" class="form-label fw-semibold">
                                    Current Password
                                </label>
                                <input type="password" 
                                       class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" 
                                       name="current_password">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="new_password" class="form-label fw-semibold">
                                    New Password
                                </label>
                                <input type="password" 
                                       class="form-control @error('new_password') is-invalid @enderror" 
                                       id="new_password" 
                                       name="new_password">
                                <small class="text-muted">Minimum 6 characters</small>
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="new_password_confirmation" class="form-label fw-semibold">
                                    Confirm New Password
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="new_password_confirmation" 
                                       name="new_password_confirmation">
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.avatar-circle i {
    font-size: 3rem;
    color: white;
}

.profile-info .info-item {
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.profile-info .info-item i {
    width: 20px;
}

.profile-info .info-item small {
    display: block;
    font-size: 0.75rem;
    margin-bottom: 0.25rem;
}

.card {
    border-radius: 12px;
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
}
</style>
@endsection
