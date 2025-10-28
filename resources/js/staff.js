// staff.js - HANYA handle create ticket form & sidebar
document.addEventListener('DOMContentLoaded', function() {
    // =========================================================================
    // CREATE TICKET FORM HANDLER (ONLY IN STAFF.JS)
    // =========================================================================
    const createTicketForm = document.getElementById('createTicketForm');
    const createTicketModal = document.getElementById('createTicketModal');

    if (createTicketForm && createTicketModal) {
        let isSubmitting = false; // Prevent double submission

        // Handler untuk form submission
        createTicketForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (isSubmitting) {
                console.log('Already submitting, please wait...');
                return;
            }
            
            submitCreateTicketForm(this);
        });

        // Handler ketika modal ditutup
        createTicketModal.addEventListener('hidden.bs.modal', function() {
            cleanupModalBackdrop();
            createTicketForm.reset();
        });

        // Handler untuk manual close buttons
        createTicketModal.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-close') || 
                e.target.closest('[data-bs-dismiss="modal"]')) {
                cleanupModalBackdrop();
            }
        });
    }

    function submitCreateTicketForm(form) {
        isSubmitting = true;
        
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Show loading
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
        submitBtn.disabled = true;

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Close modal
                const modal = bootstrap.Modal.getInstance(createTicketModal);
                modal.hide();
                
                // Cleanup
                cleanupModalBackdrop();
                
                // Show success & reload
                setTimeout(() => {
                    alert('Ticket created successfully!');
                    window.location.reload();
                }, 300);
            } else {
                throw new Error(data.message || 'Failed to create ticket');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error creating ticket: ' + error.message);
        })
        .finally(() => {
            isSubmitting = false;
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    }

    // =========================================================================
    // CLEANUP FUNCTION
    // =========================================================================
    function cleanupModalBackdrop() {
        // Remove all backdrops
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());
        
        // Reset body styles
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        
        // Hide any remaining modal shows
        const openModals = document.querySelectorAll('.modal.show');
        openModals.forEach(modal => {
            modal.classList.remove('show');
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
        });
    }

    // Emergency cleanup on page load
    if (document.querySelector('.modal-backdrop')) {
        cleanupModalBackdrop();
    }

    // =========================================================================
    // ENHANCED SIDEBAR TOGGLER - RESPONSIVE
    // =========================================================================
    const sidebarToggler = document.getElementById('sidebarToggler');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const mainContent = document.querySelector('.main-content');

    // Initialize sidebar functionality
    initSidebar();

    function initSidebar() {
        if (!sidebarToggler || !sidebar) {
            console.log('Sidebar elements not found');
            return;
        }

        console.log('🔄 Initializing enhanced sidebar...');

        // Click handler for navbar toggler
        sidebarToggler.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });

        // Close sidebar when overlay is clicked
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function(e) {
                e.preventDefault();
                closeSidebar();
            });
        }

        // Close sidebar when clicking on main content (mobile only)
        if (mainContent) {
            mainContent.addEventListener('click', function(e) {
                if (isMobile() && isSidebarOpen()) {
                    closeSidebar();
                }
            });
        }

        // Close sidebar when pressing ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isSidebarOpen()) {
                closeSidebar();
            }
        });

        // Auto-close sidebar on window resize (when switching to desktop)
        window.addEventListener('resize', function() {
            if (!isMobile() && isSidebarOpen()) {
                closeSidebar();
            }
        });

        // Close sidebar when navigating (optional)
        document.addEventListener('click', function(e) {
            if (isMobile() && isSidebarOpen() && e.target.closest('a')) {
                setTimeout(closeSidebar, 300);
            }
        });

        console.log('✅ Enhanced sidebar initialized');
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
        
        if (sidebarOverlay) {
            sidebarOverlay.classList.add('show');
        }
        
        // Add body class to prevent scrolling
        document.body.classList.add('sidebar-open');
        
        // Add backdrop for mobile
        if (isMobile()) {
            createSidebarBackdrop();
        }
        
        console.log('📱 Sidebar opened');
    }

    function closeSidebar() {
        sidebar.classList.remove('show');
        
        if (sidebarOverlay) {
            sidebarOverlay.classList.remove('show');
        }
        
        // Remove body class
        document.body.classList.remove('sidebar-open');
        
        // Remove backdrop
        removeSidebarBackdrop();
        
        console.log('📱 Sidebar closed');
    }

    function createSidebarBackdrop() {
        // Remove existing backdrop if any
        removeSidebarBackdrop();
        
        const backdrop = document.createElement('div');
        backdrop.className = 'sidebar-backdrop fade show';
        backdrop.style.cssText = `
            position: fixed;
            top: var(--navbar-height);
            left: 0;
            width: 100%;
            height: calc(100vh - var(--navbar-height));
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            cursor: pointer;
        `;
        
        backdrop.addEventListener('click', closeSidebar);
        document.body.appendChild(backdrop);
    }

    function removeSidebarBackdrop() {
        const existingBackdrop = document.querySelector('.sidebar-backdrop');
        if (existingBackdrop) {
            existingBackdrop.remove();
        }
    }

    function isSidebarOpen() {
        return sidebar.classList.contains('show');
    }

    function isMobile() {
        return window.innerWidth <= 991;
    }

    // Make functions globally available
    window.toggleSidebar = toggleSidebar;
    window.closeSidebar = closeSidebar;
    window.openSidebar = openSidebar;

    console.log('🎯 Staff.js loaded successfully');
});