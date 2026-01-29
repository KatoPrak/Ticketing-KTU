@extends('layouts.it')

@section('title', 'Feedbacks Dashboard')
@section('body-class', 'page-it-feedbacks')

<style>
.stats-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border-left: 4px solid;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.stats-card.primary { border-left-color: #0d6efd; }
.stats-card.success { border-left-color: #198754; }
.stats-card.warning { border-left-color: #ffc107; }
.stats-card.info { border-left-color: #0dcaf0; }

.stats-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    line-height: 1;
}

.stats-label {
    color: #6c757d;
    font-size: 0.875rem;
    margin-top: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stats-icon {
    font-size: 2.5rem;
    opacity: 0.2;
    position: absolute;
    right: 1.5rem;
    top: 50%;
    transform: translateY(-50%);
}

.rating-distribution {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.rating-bar {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0.75rem;
}

.rating-bar-fill {
    flex: 1;
    height: 25px;
    background: #e9ecef;
    border-radius: 20px;
    overflow: hidden;
    position: relative;
}

.rating-bar-progress {
    height: 100%;
    background: linear-gradient(90deg, #ffc107, #ffdb4d);
    transition: width 0.5s ease;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding-right: 10px;
    color: #000;
    font-weight: 600;
    font-size: 0.875rem;
}

.filter-section {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    margin-bottom: 2rem;
}

.feedback-table {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.star-display {
    color: #ffc107;
    font-size: 1.1rem;
}

.comment-preview {
    max-width: 300px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.badge-rating {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
}

.chart-container {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    margin-bottom: 2rem;
}

.page-header {
    margin-bottom: 2rem;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: #6c757d;
    font-size: 1rem;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.3;
}
</style>

@section('content')
<div class="container-fluid py-4">
    
    {{-- Page Header --}}
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-star text-warning me-2"></i>Feedbacks Dashboard
        </h1>
        <p class="page-subtitle">Monitor and analyze user satisfaction and feedback</p>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stats-card primary position-relative">
                <i class="fas fa-star stats-icon text-primary"></i>
                <h2 class="stats-number text-primary" id="avgRating">0.0</h2>
                <p class="stats-label mb-0">Average Rating</p>
                <div class="star-display mt-2" id="avgStars"></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stats-card success position-relative">
                <i class="fas fa-comments stats-icon text-success"></i>
                <h2 class="stats-number text-success" id="totalFeedbacks">0</h2>
                <p class="stats-label mb-0">Total Feedbacks</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stats-card warning position-relative">
                <i class="fas fa-thumbs-up stats-icon text-warning"></i>
                <h2 class="stats-number text-warning" id="satisfactionRate">0%</h2>
                <p class="stats-label mb-0">Satisfaction Rate</p>
                <small class="text-muted">(4-5 stars)</small>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stats-card info position-relative">
                <i class="fas fa-calendar-alt stats-icon text-info"></i>
                <h2 class="stats-number text-info" id="thisMonthCount">0</h2>
                <p class="stats-label mb-0">This Month</p>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-8">
            <div class="chart-container">
                <h5 class="mb-3">
                    <i class="fas fa-chart-line text-primary me-2"></i>Rating Trends (Last 6 Months)
                </h5>
                <canvas id="trendChart" height="80"></canvas>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="rating-distribution">
                <h5 class="mb-3">
                    <i class="fas fa-chart-bar text-warning me-2"></i>Rating Distribution
                </h5>
                <div id="ratingDistribution">
                    <div class="rating-bar">
                        <span style="width: 60px;">5 <i class="fas fa-star text-warning"></i></span>
                        <div class="rating-bar-fill">
                            <div class="rating-bar-progress" style="width: 0%" data-rating="5">
                                <span>0</span>
                            </div>
                        </div>
                    </div>
                    <div class="rating-bar">
                        <span style="width: 60px;">4 <i class="fas fa-star text-warning"></i></span>
                        <div class="rating-bar-fill">
                            <div class="rating-bar-progress" style="width: 0%" data-rating="4">
                                <span>0</span>
                            </div>
                        </div>
                    </div>
                    <div class="rating-bar">
                        <span style="width: 60px;">3 <i class="fas fa-star text-warning"></i></span>
                        <div class="rating-bar-fill">
                            <div class="rating-bar-progress" style="width: 0%" data-rating="3">
                                <span>0</span>
                            </div>
                        </div>
                    </div>
                    <div class="rating-bar">
                        <span style="width: 60px;">2 <i class="fas fa-star text-warning"></i></span>
                        <div class="rating-bar-fill">
                            <div class="rating-bar-progress" style="width: 0%" data-rating="2">
                                <span>0</span>
                            </div>
                        </div>
                    </div>
                    <div class="rating-bar">
                        <span style="width: 60px;">1 <i class="fas fa-star text-warning"></i></span>
                        <div class="rating-bar-fill">
                            <div class="rating-bar-progress" style="width: 0%" data-rating="1">
                                <span>0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filter-section">
        <form id="filterForm" class="row g-3 align-items-end">
            <div class="col-12 col-md-3">
                <label class="form-label fw-bold">
                    <i class="fas fa-star text-warning me-1"></i>Rating
                </label>
                <select class="form-select" name="rating" id="filterRating">
                    <option value="">All Ratings</option>
                    <option value="5">5 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="2">2 Stars</option>
                    <option value="1">1 Star</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label fw-bold">
                    <i class="fas fa-folder text-primary me-1"></i>Category
                </label>
                <select class="form-select" name="category" id="filterCategory">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-bold">
                    <i class="fas fa-calendar text-info me-1"></i>Date Range
                </label>
                <div class="input-group">
                    <input type="date" class="form-control" name="date_from" id="filterDateFrom">
                    <span class="input-group-text">to</span>
                    <input type="date" class="form-control" name="date_to" id="filterDateTo">
                </div>
            </div>
            <div class="col-12 col-md-2">
                <button type="button" class="btn btn-primary w-100" id="applyFilters">
                    <i class="fas fa-filter me-1"></i>Apply
                </button>
            </div>
        </form>
    </div>

    {{-- Feedback Table --}}
    <div class="feedback-table">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                <i class="fas fa-list text-primary me-2"></i>Feedback List
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle" id="feedbackTable">
                <thead class="table-light">
                    <tr>
                        <th>Ticket ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Category</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="feedbackTableBody">
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading feedbacks...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                Showing <span id="showingFrom">0</span> to <span id="showingTo">0</span> of <span id="totalRecords">0</span> feedbacks
            </div>
            <nav id="paginationNav">
                <ul class="pagination mb-0" id="paginationList">
                    <!-- Pagination will be generated by JS -->
                </ul>
            </nav>
        </div>
    </div>
</div>

{{-- View Feedback Modal --}}
<div class="modal fade" id="viewFeedbackModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-comments me-2"></i>Feedback Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Ticket ID</label>
                        <p class="form-control-plaintext" id="modal_ticket_id"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Name</label>
                        <p class="form-control-plaintext" id="modal_user"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Department</label>
                        <p class="form-control-plaintext" id="modal_department"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Category</label>
                        <p class="form-control-plaintext" id="modal_category"></p>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Rating</label>
                        <div>
                            <div class="star-display fs-3" id="modal_rating"></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Comment</label>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="mb-0" id="modal_comment"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Submitted At</label>
                        <p class="form-control-plaintext text-muted" id="modal_date"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let trendChart = null;

    // Load initial data
    loadDashboardStats();
    loadFeedbacks();

    // Apply Filters
    document.getElementById('applyFilters').addEventListener('click', function() {
        currentPage = 1;
        loadFeedbacks();
    });

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
            btn.addEventListener('click', function() {
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
            link.addEventListener('click', function(e) {
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
</script>
@endpush