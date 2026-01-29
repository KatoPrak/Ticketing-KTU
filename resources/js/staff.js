// staff.js - Fixed version with proper AJAX form handling
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎯 Staff.js initializing...');

    // =========================================================================
    // GLOBAL VARIABLES
    // =========================================================================
    window.uploadedFiles = [];
    window.isSubmitting = false;

    // =========================================================================
    // CLEANUP FUNCTIONS
    // =========================================================================
    window.forceCleanupBackdrops = function() {
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

    window.emergencyCleanup = function() {
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
    window.showSuccessAlert = function(message, callback) {
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

    window.showErrorAlert = function(message) {
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
    // CREATE TICKET FORM HANDLER - FIXED AJAX VERSION
    // =========================================================================
    const createTicketForm = document.getElementById('createTicketForm');
    const createTicketModal = document.getElementById('createTicketModal');

    if (createTicketForm && createTicketModal) {
        console.log('✅ Create ticket form found, initializing...');

        // PREVENT DEFAULT FORM SUBMISSION
        createTicketForm.addEventListener('submit', function(e) {
            e.preventDefault(); // CRITICAL: Stop normal form submission
            e.stopPropagation();
            
            console.log('📝 Form submit intercepted');
            
            if (window.isSubmitting) {
                console.log('⚠️ Already submitting, please wait...');
                return false;
            }
            
            submitCreateTicketForm(this);
            return false; // Extra safety
        });

        // Reset form when modal closes
        createTicketModal.addEventListener('hidden.bs.modal', function() {
            console.log('📝 Modal hidden, resetting form...');
            createTicketForm.reset();
            window.isSubmitting = false;
            
            const submitBtn = createTicketForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Submit Ticket';
            }
            
            setTimeout(() => {
                window.forceCleanupBackdrops();
            }, 100);
        });

        // Cleanup when modal opens
        createTicketModal.addEventListener('show.bs.modal', function() {
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

        sidebarToggler.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function(e) {
                e.preventDefault();
                closeSidebar();
            });
        }

        document.addEventListener('keydown', function(e) {
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

    // Make functions globally available
    window.toggleSidebar = toggleSidebar;
    window.closeSidebar = closeSidebar;
    window.openSidebar = openSidebar;

    // Initialize sidebar
    initSidebar();

    // Initial cleanup
    setTimeout(() => {
        if (document.querySelector('.modal-backdrop')) {
            console.log('🆗 Found existing backdrops on page load, cleaning up...');
            window.emergencyCleanup();
        }
    }, 100);

    window.addEventListener('beforeunload', window.emergencyCleanup);

    console.log('🎯 Staff.js loaded successfully');
});

// Global cleanup function for debugging
window.cleanupAllBackdrops = function() {
    console.log('🛠 Manual cleanup triggered');
    window.emergencyCleanup();
};
// =========================================================================
    // TICKET DETAIL MODAL HANDLER (Delegated to ticket-detail-handler.js)
    // =========================================================================
    console.log('📋 Ticket detail modal handler delegated to separate file');
    
    // Make sure Bootstrap modals cleanup properly
    document.addEventListener('hidden.bs.modal', function (event) {
        const modal = event.target;
        if (modal.id === 'detailTicketModal') {
            console.log('📋 Detail modal closed, cleaning up...');
            setTimeout(() => {
                window.forceCleanupBackdrops();
            }, 100);
        }
    });