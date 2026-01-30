document.addEventListener('DOMContentLoaded', function () {
    // Clear search input
    document.getElementById('clearSearch').addEventListener('click', function () {
        document.getElementById('search').value = '';
        document.getElementById('filterForm').submit();
    });

    // Set max date for end_date to today
    const today = new Date().toISOString().split('T')[0];
    const endDateInput = document.getElementById('end_date');
    if (endDateInput) {
        endDateInput.max = today;

        endDateInput.addEventListener('change', function () {
            const startDate = document.getElementById('start_date');
            if (this.value && startDate.value && this.value < startDate.value) {
                startDate.value = this.value;
            }
        });
    }

    // Validate date range
    const startDateInput = document.getElementById('start_date');
    if (startDateInput) {
        startDateInput.addEventListener('change', function () {
            const endDate = document.getElementById('end_date');
            if (this.value && endDate.value && this.value > endDate.value) {
                endDate.value = this.value;
            }
        });
    }
});
