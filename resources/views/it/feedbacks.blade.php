@extends('layouts.it')

@section('title', 'Feedbacks Dashboard')
@section('body-class', 'page-it-feedbacks')

@push('styles')
    @vite('resources/css/feedbacks.css')
@endpush

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
    @vite('resources/js/feedbacks.js')
@endpush