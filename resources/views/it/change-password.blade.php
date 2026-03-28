@extends('layouts.it')

@section('title', 'Change Password')


@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite('resources/css/change-password.css')
@endpush
@section('content')
<div class="change-password-wrapper">
    <div class="change-password-container">
        <!-- Page Header -->
        <div class="page-header">
            <i class="fas fa-shield-alt"></i>
            <h1>Change Password</h1>
            <p>Enhance Your Account Security</p>
        </div>

        <!-- Password Form Card -->
        <div class="password-card">
            <div class="card-body">
                <form id="changePasswordForm">
                    <!-- Current Password -->
                    <div class="input-wrapper">
                        <label for="currentPassword">Current Password</label>
                        <div class="input-group">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="currentPassword" placeholder="Enter current password" required>
                            <i class="far fa-eye toggle-password" id="toggleCurrentPassword"></i>
                        </div>
                    </div>

                    <!-- New Password -->
                    <div class="input-wrapper">
                        <label for="newPassword">New Password</label>
                        <div class="input-group">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="newPassword" placeholder="Create new password" required>
                            <i class="far fa-eye toggle-password" id="toggleNewPassword"></i>
                        </div>
                        
                        <!-- Strength Indicator -->
                        <div class="strength-indicator" id="strengthIndicator" style="display: none;">
                            <div class="strength-bar-container">
                                <div class="strength-bar" id="strengthBar"></div>
                            </div>
                            <div class="strength-label">
                                Strength: <strong id="strengthText">-</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="input-wrapper">
                        <label for="confirmPassword">Confirm Password</label>
                        <div class="input-group">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="confirmPassword" placeholder="Re-enter new password" required>
                            <i class="far fa-eye toggle-password" id="toggleConfirmPassword"></i>
                        </div>
                    </div>

                    <!-- Requirements Box -->
                    <div class="requirements-box">
                        <h4>
                            <i class="fas fa-clipboard-check"></i>
                            Password Requirements
                        </h4>
                        <ul class="requirements-list">
                            <li id="req-length" class="requirement-not-met">
                                <i class="fas fa-times"></i>
                                Minimum 8 characters
                            </li>
                        </ul>
                    </div>

                    <!-- Buttons -->
                    <div class="button-group">
                        <button type="submit" class="btn-custom btn-primary-custom" id="submitBtn">
                            <i class="fas fa-save"></i>
                            Save Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('it.partials.sidebar')
@include('it.partials.navbar')

<div class="notification-custom" id="notification"></div>
@endsection

@push('scripts')
    @vite('resources/js/change-password.js')
@endpush