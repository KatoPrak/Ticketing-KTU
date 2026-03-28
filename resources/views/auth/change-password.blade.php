@extends('layouts.staff')

@section('title', 'Change Password')


@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@endpush
<style>
    /* Reset z-index for all elements on this page */
    * {
        position: relative;
    }

    .change-password-wrapper {
        padding: 20px;
        background: #f8f9fa;
        min-height: calc(100vh - 60px);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 1;
    }

    .change-password-container {
        max-width: 550px;
        width: 100%;
        position: relative;
        z-index: 1;
    }

    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        color: white;
        margin-bottom: 20px;
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        position: relative;
        z-index: 1;
    }

    .page-header i {
        font-size: 36px;
        margin-bottom: 8px;
    }

    .page-header h1 {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .page-header p {
        font-size: 13px;
        opacity: 0.95;
        margin: 0;
    }

    .password-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        position: relative;
        z-index: 1;
    }

    .card-body {
        padding: 25px;
    }

    .input-wrapper {
        margin-bottom: 18px;
        position: relative;
        z-index: 1;
    }

    .input-wrapper label {
        display: block;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 6px;
        font-size: 13px;
    }

    .input-group {
        position: relative;
        z-index: 1;
    }

    .input-group input {
        width: 100%;
        padding: 10px 38px 10px 38px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: #f8fafc;
    }

    .input-group input:focus {
        border-color: #667eea;
        background: white;
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .input-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
        font-size: 14px;
        z-index: 2;
    }

    .toggle-password {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #a0aec0;
        transition: color 0.3s;
        z-index: 2;
    }

    .toggle-password:hover {
        color: #667eea;
    }

    .strength-indicator {
        margin-top: 8px;
    }

    .strength-bar-container {
        height: 6px;
        background: #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 5px;
    }

    .strength-bar {
        height: 100%;
        width: 0;
        transition: all 0.4s ease;
        border-radius: 10px;
    }

    .strength-label {
        font-size: 11px;
        font-weight: 600;
    }

    .requirements-box {
        background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
        border-radius: 10px;
        padding: 15px;
        margin: 18px 0;
        border-left: 3px solid #667eea;
        position: relative;
        z-index: 1;
    }

    .requirements-box h4 {
        font-size: 13px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .requirements-box h4 i {
        color: #667eea;
    }

    .requirements-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .requirements-list li {
        padding: 5px 0;
        display: flex;
        align-items: center;
        font-size: 12px;
        color: #4a5568;
    }

    .requirements-list li i {
        margin-right: 6px;
        font-size: 12px;
        width: 16px;
        height: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .requirement-met i {
        background: #48bb78;
        color: white;
    }

    .requirement-not-met i {
        background: #e2e8f0;
        color: #a0aec0;
    }

    .button-group {
        display: flex;
        gap: 12px;
        margin-top: 20px;
    }

    .btn-custom {
        flex: 1;
        padding: 11px 20px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(102, 126, 234, 0.5);
    }

    .btn-secondary-custom {
        background: white;
        color: #4a5568;
        border: 2px solid #e2e8f0;
    }

    .btn-secondary-custom:hover {
        background: #f8fafc;
        border-color: #cbd5e0;
    }

    .notification-custom {
        position: fixed;
        top: 20px;
        right: 20px;
        min-width: 280px;
        padding: 14px 20px;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        font-size: 14px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        transform: translateX(400px);
        transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .notification-custom.show {
        transform: translateX(0);
    }

    .notification-custom.success {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    }

    .notification-custom.error {
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
    }

    /* IMPORTANT: Navbar and Dropdown Z-Index Fix */
    nav,
    .navbar,
    header,
    .top-navbar,
    .main-navbar {
        position: relative !important;
        z-index: 1000 !important;
    }

    .dropdown,
    .user-dropdown,
    .profile-dropdown,
    .nav-dropdown,
    .navbar-dropdown {
        position: relative !important;
        z-index: 1001 !important;
    }

    .dropdown-menu,
    .dropdown-content,
    .user-menu,
    .profile-menu,
    .dropdown-list {
        position: absolute !important;
        z-index: 1002 !important;
    }

    .dropdown-menu.show,
    .dropdown-content.show,
    .user-menu.show,
    .profile-menu.show,
    .dropdown.active .dropdown-menu,
    .dropdown.active .dropdown-content {
        z-index: 1002 !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Force all navbar related elements to be on top */
    .navbar *,
    nav * {
        position: relative;
        z-index: inherit;
    }

    /* Ensure content doesn't overlap navbar */
    .change-password-wrapper,
    .change-password-container,
    .password-card,
    .page-header {
        position: relative;
        z-index: 1;
    }

    @media (max-width: 768px) {
        .change-password-wrapper {
            padding: 15px;
            align-items: flex-start;
        }

        .page-header {
            padding: 20px;
        }

        .card-body {
            padding: 20px;
        }

        .button-group {
            flex-direction: column;
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(15px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .password-card {
        animation: fadeIn 0.4s ease;
    }
</style>
@section('content')
@include('staff.partials.navbar')
@include('staff.partials.sidebar')

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
                    @csrf
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

<div class="notification-custom" id="notification"></div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elements
        const form = document.getElementById('changePasswordForm');
        const currentPassword = document.getElementById('currentPassword');
        const newPassword = document.getElementById('newPassword');
        const confirmPassword = document.getElementById('confirmPassword');
        const strengthIndicator = document.getElementById('strengthIndicator');
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        const notification = document.getElementById('notification');
        
        // Toggle buttons
        const toggles = {
            current: document.getElementById('toggleCurrentPassword'),
            new: document.getElementById('toggleNewPassword'),
            confirm: document.getElementById('toggleConfirmPassword')
        };
        
        // Requirements
        const requirements = {
            length: document.getElementById('req-length')
        };

        // Toggle password visibility
        function setupToggle(input, toggle) {
            toggle.addEventListener('click', function() {
                const type = input.type === 'password' ? 'text' : 'password';
                input.type = type;
                
                this.classList.remove('fa-eye', 'fa-eye-slash');
                this.classList.add(type === 'password' ? 'fa-eye' : 'fa-eye-slash');
            });
        }

        setupToggle(currentPassword, toggles.current);
        setupToggle(newPassword, toggles.new);
        setupToggle(confirmPassword, toggles.confirm);

        // Update requirement UI
        function updateRequirement(element, met) {
            if (met) {
                element.classList.remove('requirement-not-met');
                element.classList.add('requirement-met');
                element.querySelector('i').classList.remove('fa-times');
                element.querySelector('i').classList.add('fa-check');
            } else {
                element.classList.remove('requirement-met');
                element.classList.add('requirement-not-met');
                element.querySelector('i').classList.remove('fa-check');
                element.querySelector('i').classList.add('fa-times');
            }
        }

        // Check password strength
        function checkPasswordStrength(password) {
            const lengthMet = password.length >= 8;
            
            // Update requirement
            updateRequirement(requirements.length, lengthMet);

            // Update strength bar
            if (password.length > 0) {
                strengthIndicator.style.display = 'block';
                
                let strength = Math.min((password.length / 8) * 100, 100);
                strengthBar.style.width = strength + '%';

                if (strength < 50) {
                    strengthBar.style.background = 'linear-gradient(90deg, #f56565, #e53e3e)';
                    strengthText.textContent = 'Weak';
                    strengthText.style.color = '#e53e3e';
                } else if (strength < 80) {
                    strengthBar.style.background = 'linear-gradient(90deg, #ecc94b, #d69e2e)';
                    strengthText.textContent = 'Good';
                    strengthText.style.color = '#d69e2e';
                } else {
                    strengthBar.style.background = 'linear-gradient(90deg, #48bb78, #38a169)';
                    strengthText.textContent = 'Strong';
                    strengthText.style.color = '#38a169';
                }
            } else {
                strengthIndicator.style.display = 'none';
            }

            return lengthMet;
        }

        // Show notification
        function showNotification(message, type) {
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                <span>${message}</span>
            `;
            notification.className = `notification-custom ${type}`;
            notification.classList.add('show');

            setTimeout(() => {
                notification.classList.remove('show');
            }, 4000);
        }

        // Validate form
        function validateForm() {
            if (!currentPassword.value.trim()) {
                showNotification('Current password is required', 'error');
                currentPassword.focus();
                return false;
            }

            if (newPassword.value.length < 8) {
                showNotification('Password must be at least 8 characters', 'error');
                newPassword.focus();
                return false;
            }

            if (newPassword.value !== confirmPassword.value) {
                showNotification('Password confirmation does not match', 'error');
                confirmPassword.focus();
                return false;
            }

            return true;
        }

        // Event listeners
        newPassword.addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });

        // Form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            if (validateForm()) {
                const submitBtn = document.getElementById('submitBtn');
                const originalContent = submitBtn.innerHTML;
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

                // Get CSRF token
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                // Send request to backend
                fetch('/change-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        current_password: currentPassword.value,
                        new_password: newPassword.value,
                        new_password_confirmation: confirmPassword.value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message || 'Password changed successfully!', 'success');
                        
                        // Reset form
                        form.reset();
                        strengthIndicator.style.display = 'none';
                        updateRequirement(requirements.length, false);
                    } else {
                        showNotification(data.message || 'Failed to change password', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred. Please try again.', 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                });
            }
        });
    });

    // Fix dropdown click event - ensure it's not blocked
    document.addEventListener('click', function(e) {
        // Don't interfere with dropdown clicks
        if (e.target.closest('.dropdown, .user-dropdown, .profile-dropdown')) {
            console.log('Dropdown clicked:', e.target);
        }
    }, true); // Use capture phase

    // Debug dropdown
    setTimeout(() => {
        const dropdowns = document.querySelectorAll('.dropdown, .user-dropdown, .profile-dropdown');
        console.log('Found dropdowns:', dropdowns.length);
        dropdowns.forEach((dropdown, index) => {
            console.log(`Dropdown ${index}:`, dropdown);
            const menu = dropdown.querySelector('.dropdown-menu, .dropdown-content, .user-menu');
            if (menu) {
                console.log(`Dropdown ${index} has menu:`, menu);
            }
        });
    }, 500);
</script>
@endpush