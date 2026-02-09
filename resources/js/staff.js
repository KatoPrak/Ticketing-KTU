// staff.js - Fixed version with proper AJAX form handling

// =========================================================================
// SUBMIT CREATE TICKET FORM - AJAX HANDLER (DEFINED GLOBALLY FIRST)
// =========================================================================
window.submitCreateTicketForm = function (form) {
    console.log('📤 Submitting create ticket form...');

    if (window.isSubmitting) {
        console.log('⚠️ Already submitting, ignoring...');
        return;
    }

    window.isSubmitting = true;

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn ? submitBtn.innerHTML : '';

    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Submitting...';
    }

    const formData = new FormData(form);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    fetch('/staff/tickets', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            console.log('✅ Ticket created successfully:', data);

            const createTicketModal = document.getElementById('createTicketModal');
            const modal = createTicketModal ? bootstrap.Modal.getInstance(createTicketModal) : null;
            if (modal) {
                modal.hide();
            }

            if (typeof window.showSuccessAlert === 'function') {
                window.showSuccessAlert(data.message || 'Ticket created successfully!', () => window.location.reload());
            } else {
                alert(data.message || 'Ticket created successfully!');
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('❌ Error creating ticket:', error);

            let errorMessage = 'Failed to create ticket. Please try again.';

            if (error.message) {
                errorMessage = error.message;
            } else if (error.errors) {
                const firstError = Object.values(error.errors)[0];
                errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
            }

            if (typeof window.showErrorAlert === 'function') {
                window.showErrorAlert(errorMessage);
            } else {
                alert(errorMessage);
            }

            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
            window.isSubmitting = false;
        });
};

document.addEventListener('DOMContentLoaded', function () {
    console.log('🎯 Staff.js initializing...');

    // =========================================================================
    // GLOBAL VARIABLES
    // =========================================================================
    window.uploadedFiles = [];
    window.isSubmitting = false;

    // =========================================================================
    // CLEANUP FUNCTIONS
    // =========================================================================
    window.forceCleanupBackdrops = function () {
        console.log('🔄 Force cleaning up backdrops...');

        const backdrops = document.querySelectorAll('.modal-backdrop, .sidebar-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());

        document.body.classList.remove('modal-open', 'sidebar-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';

        const openModals = document.querySelectorAll('.modal.show');
        openModals.forEach(modal => {
            modal.classList.remove('show');
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
        });
    };

    window.emergencyCleanup = function () {
        console.log('🆗 Emergency cleanup triggered');
        window.forceCleanupBackdrops();

        const stuckElements = document.querySelectorAll('[style*="overflow"], [style*="padding-right"]');
        stuckElements.forEach(el => {
            if (el !== document.body) {
                el.style.overflow = '';
                el.style.paddingRight = '';
            }
        });
    };

    // =========================================================================
    // ALERT FUNCTIONS
    // =========================================================================
    window.showSuccessAlert = function (message, callback) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Success!',
                text: message,
                icon: 'success',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed && typeof callback === 'function') {
                    callback();
                }
            });
        } else {
            window.forceCleanupBackdrops();
            alert(message);
            if (typeof callback === 'function') {
                callback();
            }
        }
    };

    window.showErrorAlert = function (message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Error!',
                text: message,
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        } else {
            alert(message);
        }
    };

    // =========================================================================
    // CREATE TICKET FORM HANDLER
    // =========================================================================
    const createTicketForm = document.getElementById('createTicketForm');
    const createTicketModal = document.getElementById('createTicketModal');

    if (createTicketForm && createTicketModal) {
        console.log('✅ Create ticket form found, initializing...');

        createTicketForm.addEventListener('submit', function (e) {
            e.preventDefault();
            e.stopPropagation();

            console.log('📝 Form submit intercepted');

            if (window.isSubmitting) {
                console.log('⚠️ Already submitting, please wait...');
                return false;
            }

            window.submitCreateTicketForm(this);
            return false;
        });

        createTicketModal.addEventListener('hidden.bs.modal', function () {
            console.log('📝 Modal hidden, resetting form...');
            createTicketForm.reset();
            window.isSubmitting = false;

            const submitBtn = createTicketForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Submit Ticket';
            }

            setTimeout(() => window.forceCleanupBackdrops(), 100);
        });

        createTicketModal.addEventListener('show.bs.modal', function () {
            console.log('📝 Modal opening, cleanup...');
            window.forceCleanupBackdrops();
        });
    }

    // =========================================================================
    // SIDEBAR HANDLER
    // =========================================================================
    const sidebarToggler = document.getElementById('sidebarToggler');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    function initSidebar() {
        if (!sidebarToggler || !sidebar) return;

        console.log('🔄 Initializing sidebar...');

        sidebarToggler.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function (e) {
                e.preventDefault();
                closeSidebar();
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && isSidebarOpen()) {
                closeSidebar();
            }
        });
    }

    function toggleSidebar() {
        if (sidebar.classList.contains('show')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    }

    function openSidebar() {
        sidebar.classList.add('show');
        if (sidebarOverlay) sidebarOverlay.classList.add('show');
        document.body.classList.add('sidebar-open');
        console.log('📱 Sidebar opened');
    }

    function closeSidebar() {
        sidebar.classList.remove('show');
        if (sidebarOverlay) sidebarOverlay.classList.remove('show');
        document.body.classList.remove('sidebar-open');
        console.log('📱 Sidebar closed');
    }

    function isSidebarOpen() {
        return sidebar && sidebar.classList.contains('show');
    }

    window.toggleSidebar = toggleSidebar;
    window.closeSidebar = closeSidebar;
    window.openSidebar = openSidebar;

    initSidebar();

    setTimeout(() => {
        if (document.querySelector('.modal-backdrop')) {
            console.log('🆗 Found existing backdrops on page load, cleaning up...');
            window.emergencyCleanup();
        }
    }, 100);

    window.addEventListener('beforeunload', window.emergencyCleanup);

    console.log('🎯 Staff.js loaded successfully');
});

window.cleanupAllBackdrops = function () {
    console.log('🛠 Manual cleanup triggered');
    window.emergencyCleanup();
};

document.addEventListener('hidden.bs.modal', function (event) {
    const modal = event.target;
    if (modal.id === 'detailTicketModal') {
        console.log('📋 Detail modal closed, cleaning up...');
        setTimeout(() => window.forceCleanupBackdrops(), 100);
    }
});