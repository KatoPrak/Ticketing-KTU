/**
 * Auto Logout on Inactivity
 * This script monitors user activity and automatically logs out the user
 * after a specified period of inactivity.
 */

(function () {
    // Inactivity timeout in milliseconds (e.g., 30 minutes = 30 * 60 * 1000)
    // You can adjust this value according to your needs.
    const INACTIVITY_TIMEOUT = 30 * 60 * 1000;

    let timeoutId;

    function resetTimer() {
        if (timeoutId) {
            clearTimeout(timeoutId);
        }

        timeoutId = setTimeout(logout, INACTIVITY_TIMEOUT);
    }

    function logout() {
        const logoutForm = document.getElementById('logout-form');

        if (logoutForm) {
            // Optional: Show a message before logging out or just do it
            console.log('User inactive. Logging out...');

            // We can use SweetAlert2 if available for a smoother experience
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Session Expired',
                    text: 'You have been inactive for a while. Logging out for security...',
                    icon: 'info',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    willClose: () => {
                        logoutForm.submit();
                    }
                });
            } else {
                logoutForm.submit();
            }
        } else {
            // Fallback for pages without logout form
            window.location.href = '/logout';
        }
    }

    // List of events that reset the timer
    const activityEvents = [
        'mousedown', 'mousemove', 'keypress',
        'scroll', 'touchstart', 'click'
    ];

    // Start monitoring
    activityEvents.forEach(event => {
        document.addEventListener(event, resetTimer, true);
    });

    // Initial start
    resetTimer();

    console.log('Inactivity monitor started. Timeout:', INACTIVITY_TIMEOUT / 60 / 1000, 'minutes');
})();
