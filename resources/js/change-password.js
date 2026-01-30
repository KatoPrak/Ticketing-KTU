document.addEventListener('DOMContentLoaded', function () {
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
        toggle.addEventListener('click', function () {
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
    newPassword.addEventListener('input', function () {
        checkPasswordStrength(this.value);
    });

    // Form submission
    form.addEventListener('submit', function (e) {
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

    // Cancel button
    const cancelBtn = document.getElementById('cancelBtn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function () {
            if (confirm('Are you sure you want to cancel?')) {
                form.reset();
                strengthIndicator.style.display = 'none';
                updateRequirement(requirements.length, false);

                showNotification('Changes cancelled', 'error');
            }
        });
    }
});
