document.addEventListener('DOMContentLoaded', function () {
    let currentPage = 1;
    let trendChart = null;

    // Load initial data
    loadDashboardStats();
    loadFeedbacks();

    // Apply Filters
    const applyFiltersBtn = document.getElementById('applyFilters');
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function () {
            currentPage = 1;
            loadFeedbacks();
        });
    }

    // Load Dashboard Stats
    async function loadDashboardStats() {
        try {
            const response = await fetch('/it/feedbacks/stats');
            const data = await response.json();

            if (data.success) {
                // Update stats cards
                document.getElementById('avgRating').textContent = data.stats.avgRating.toFixed(1);
                document.getElementById('totalFeedbacks').textContent = data.stats.totalFeedbacks;
                document.getElementById('satisfactionRate').textContent = data.stats.satisfactionRate + '%';
                document.getElementById('thisMonthCount').textContent = data.stats.thisMonthCount;

                // Update average stars display
                const avgStars = Math.round(data.stats.avgRating);
                let starsHtml = '';
                for (let i = 1; i <= 5; i++) {
                    starsHtml += i <= avgStars ? '<i class="fas fa-star"></i> ' : '<i class="far fa-star"></i> ';
                }
                document.getElementById('avgStars').innerHTML = starsHtml;

                // Update rating distribution
                updateRatingDistribution(data.stats.distribution);

                // Update trend chart
                updateTrendChart(data.stats.trends);
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    // Update Rating Distribution
    function updateRatingDistribution(distribution) {
        const total = Object.values(distribution).reduce((a, b) => a + b, 0);

        for (let i = 1; i <= 5; i++) {
            const count = distribution[i] || 0;
            const percentage = total > 0 ? (count / total * 100) : 0;
            const bar = document.querySelector(`[data-rating="${i}"]`);
            if (bar) {
                bar.style.width = percentage + '%';
                bar.querySelector('span').textContent = count;
            }
        }
    }

    // Update Trend Chart
    function updateTrendChart(trends) {
        const ctx = document.getElementById('trendChart').getContext('2d');

        if (trendChart) {
            trendChart.destroy();
        }

        trendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: trends.labels,
                datasets: [{
                    label: 'Average Rating',
                    data: trends.data,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Load Feedbacks
    async function loadFeedbacks() {
        const tableBody = document.getElementById('feedbackTableBody');
        tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </td>
            </tr>
        `;

        try {
            const formData = new FormData(document.getElementById('filterForm'));
            const params = new URLSearchParams(formData);
            params.append('page', currentPage);

            const response = await fetch(`/it/feedbacks/list?${params}`);
            const data = await response.json();

            if (data.success) {
                renderFeedbacks(data.feedbacks);
                updatePagination(data.pagination);
            }
        } catch (error) {
            console.error('Error loading feedbacks:', error);
            tableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                        <p>Failed to load feedbacks</p>
                    </td>
                </tr>
            `;
        }
    }

    // Render Feedbacks
    function renderFeedbacks(feedbacks) {
        const tableBody = document.getElementById('feedbackTableBody');

        if (feedbacks.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>No feedbacks found</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        tableBody.innerHTML = feedbacks.map(fb => {
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                stars += i <= fb.rating ? '<i class="fas fa-star text-warning"></i> ' : '<i class="far fa-star text-muted"></i> ';
            }

            return `
                <tr>
                    <td><strong>${fb.ticket_id}</strong></td>
                    <td>${fb.user_name}</td>
                    <td>${fb.department}</td>
                    <td><span class="badge bg-info">${fb.category}</span></td>
                    <td><div class="star-display">${stars}</div></td>
                    <td><div class="comment-preview">${fb.comment}</div></td>
                    <td><small>${fb.created_at}</small></td>
                    <td>
                        <button class="btn btn-sm btn-primary view-feedback" data-feedback='${JSON.stringify(fb)}'>
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

        // Attach event listeners
        document.querySelectorAll('.view-feedback').forEach(btn => {
            btn.addEventListener('click', function () {
                const feedback = JSON.parse(this.getAttribute('data-feedback'));
                showFeedbackModal(feedback);
            });
        });
    }

    // Show Feedback Modal
    function showFeedbackModal(feedback) {
        document.getElementById('modal_ticket_id').textContent = feedback.ticket_id;
        document.getElementById('modal_user').textContent = feedback.user_name;
        document.getElementById('modal_department').textContent = feedback.department;
        document.getElementById('modal_category').textContent = feedback.category;
        document.getElementById('modal_comment').textContent = feedback.comment;
        document.getElementById('modal_date').textContent = feedback.created_at;

        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += i <= feedback.rating ? '<i class="fas fa-star"></i> ' : '<i class="far fa-star text-muted"></i> ';
        }
        document.getElementById('modal_rating').innerHTML = stars;

        const modal = new bootstrap.Modal(document.getElementById('viewFeedbackModal'));
        modal.show();
    }

    // Update Pagination
    function updatePagination(pagination) {
        document.getElementById('showingFrom').textContent = pagination.from || 0;
        document.getElementById('showingTo').textContent = pagination.to || 0;
        document.getElementById('totalRecords').textContent = pagination.total || 0;

        const paginationList = document.getElementById('paginationList');
        paginationList.innerHTML = '';

        if (pagination.total === 0) return;

        // Previous button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${pagination.current_page === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link" href="#" data-page="${pagination.current_page - 1}">Previous</a>`;
        paginationList.appendChild(prevLi);

        // Page numbers
        for (let i = 1; i <= pagination.last_page; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === pagination.current_page ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
            paginationList.appendChild(li);
        }

        // Next button
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link" href="#" data-page="${pagination.current_page + 1}">Next</a>`;
        paginationList.appendChild(nextLi);

        // Attach click events
        paginationList.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const page = parseInt(this.getAttribute('data-page'));
                if (page > 0 && page <= pagination.last_page) {
                    currentPage = page;
                    loadFeedbacks();
                }
            });
        });
    }
});
