@extends('layouts.it')

@section('title', 'News Management')


@section('content')
<div class="container py-4">
    {{-- Header Section --}}
    <div class="page-header-animated mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="header-title-group">
                <div class="icon-wrapper">
                    <i class="fas fa-newspaper"></i>
                </div>
                <div>
                    <h2 class="page-title mb-1">News Management</h2>
                    <p class="page-subtitle mb-0">Manage all announcements and updates</p>
                </div>
            </div>
            <a href="{{ route('it.news.create') }}" class="btn btn-primary btn-add">
                <i class="fas fa-plus-circle me-2"></i>Add News
            </a>
        </div>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="alert alert-success-modern alert-dismissible fade show" role="alert">
            <div class="alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="alert-content">
                <strong>Success!</strong> {{ session('success') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- News Table Card --}}
    <div class="card border-0 shadow-lg table-card">
        <div class="card-header-custom">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1"><i class="fas fa-list me-2"></i>All Announcements</h5>
                    <p class="text-muted small mb-0">View and manage your news items</p>
                </div>
                <div class="search-box d-none d-md-block">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" class="form-control" placeholder="Search news...">
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-modern align-middle mb-0" id="newsTable">
                <thead>
                    <tr>
                        <th style="width:5%">#</th>
                        <th>Message</th>
                        <th style="width:18%">Created At</th>
                        <th class="text-center" style="width:15%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($news as $item)
                    <tr class="news-row" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                        <td class="fw-semibold text-muted">{{ $loop->iteration }}</td>
                        <td>
                            <div class="news-message">
                                <i class="fas fa-comment-dots me-2 text-primary"></i>
                                <span class="message-text" title="{{ $item->message }}" data-bs-toggle="tooltip">
                                    {{ Str::limit($item->message, 50) }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="date-badge">
                                <i class="far fa-calendar-alt me-1"></i>
                                {{ $item->created_at->format('d M Y') }}
                                <br>
                                <small class="text-muted">
                                    <i class="far fa-clock me-1"></i>{{ $item->created_at->format('H:i') }}
                                </small>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="action-buttons">
                                <button type="button"
                                    class="btn btn-sm btn-warning-modern"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal"
                                    data-bs-url="{{ route('it.news.update', $item->id) }}"
                                    data-news='@json(["id" => $item->id, "message" => $item->message])'
                                    title="Edit">
                                    <i class="fas fa-edit"></i>
                                    <span class="btn-text">Edit</span>
                                </button>

                                <button type="button" 
                                    class="btn btn-sm btn-danger-modern" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteModal"
                                    data-bs-url="{{ route('it.news.destroy', $item->id) }}"
                                    title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                    <span class="btn-text">Delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-inbox"></i>
                                </div>
                                <h5 class="empty-title">No Announcements Yet</h5>
                                <p class="empty-subtitle">Start by creating your first news announcement</p>
                                <a href="{{ route('it.news.create') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-plus-circle me-2"></i>Create First Announcement
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modern-modal">
            <div class="modal-header-modern bg-warning">
                <div class="d-flex align-items-center">
                    <div class="modal-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0">Edit Announcement</h5>
                        <p class="modal-subtitle mb-0">Update your announcement message</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body-modern">
                    <div class="form-group-modern">
                        <label for="editMessage" class="form-label-modern">
                            <i class="fas fa-comment-alt me-2"></i>Message
                            <span class="text-danger">*</span>
                        </label>
                        <textarea name="message" id="editMessage" class="form-control-modern" rows="5" required placeholder="Enter your message here..."></textarea>
                        <div class="char-counter-edit mt-2">
                            <i class="fas fa-keyboard me-1"></i>
                            <span id="editCharCount">0</span> characters
                        </div>
                    </div>
                </div>
                <div class="modal-footer-modern">
                    <button type="button" class="btn btn-light btn-cancel" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modern-modal">
            <div class="modal-header-modern bg-danger">
                <div class="d-flex align-items-center">
                    <div class="modal-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0">Confirm Delete</h5>
                        <p class="modal-subtitle mb-0">This action cannot be undone</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body-modern">
                <div class="alert alert-warning-custom">
                    <i class="fas fa-info-circle me-2"></i>
                    Are you sure you want to delete this announcement? This action is permanent and cannot be reversed.
                </div>
            </div>
            <div class="modal-footer-modern">
                <button type="button" class="btn btn-light btn-cancel" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-2"></i>Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* ========================================
   PAGE HEADER
======================================== */
.page-header-animated {
    animation: fadeInDown 0.6s ease-out;
}

.header-title-group {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.icon-wrapper {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
    animation: float 3s ease-in-out infinite;
}

.icon-wrapper i {
    font-size: 1.75rem;
    color: white;
}

.page-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1a202c;
    margin: 0;
}

.page-subtitle {
    color: #718096;
    font-size: 0.95rem;
}

.btn-add {
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

/* ========================================
   SUCCESS ALERT
======================================== */
.alert-success-modern {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border: none;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    animation: slideInDown 0.5s ease-out;
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.15);
}

.alert-icon {
    width: 40px;
    height: 40px;
    background: #28a745;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.alert-icon i {
    color: white;
    font-size: 1.25rem;
}

.alert-content {
    flex: 1;
    color: #155724;
}

/* ========================================
   TABLE CARD
======================================== */
.table-card {
    border-radius: 16px;
    overflow: hidden;
    animation: slideInUp 0.6s ease-out;
}

.card-header-custom {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 1.5rem 2rem;
    color: white;
}

.card-header-custom h5 {
    color: white;
    font-weight: 700;
    margin: 0;
}

.card-header-custom p {
    color: rgba(255, 255, 255, 0.9);
}

.search-box {
    position: relative;
    width: 280px;
}

.search-box i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #a0aec0;
    font-size: 0.9rem;
}

.search-box input {
    padding: 0.625rem 1rem 0.625rem 2.75rem;
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.15);
    color: white;
    font-size: 0.9rem;
    backdrop-filter: blur(10px);
}

.search-box input::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

.search-box input:focus {
    outline: none;
    background: rgba(255, 255, 255, 0.25);
    border-color: rgba(255, 255, 255, 0.4);
}

/* ========================================
   TABLE MODERN
======================================== */
.table-modern {
    margin: 0;
}

.table-modern thead th {
    background: #f7fafc;
    color: #4a5568;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 1.25rem 1.5rem;
    border: none;
}

.table-modern tbody td {
    padding: 1.25rem 1.5rem;
    vertical-align: middle;
    border-bottom: 1px solid #e2e8f0;
}

.table-modern tbody tr {
    transition: all 0.3s ease;
}

.table-modern tbody tr:hover {
    background: #f7fafc;
    transform: translateX(5px);
}

.news-message {
    display: flex;
    align-items: center;
}

.message-text {
    color: #2d3748;
    font-size: 0.95rem;
    line-height: 1.5;
}

.date-badge {
    color: #4a5568;
    font-size: 0.9rem;
    font-weight: 500;
}

.date-badge small {
    font-size: 0.8rem;
}

/* ========================================
   ACTION BUTTONS
======================================== */
.action-buttons {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-warning-modern,
.btn-danger-modern {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.85rem;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.btn-warning-modern {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: white;
}

.btn-warning-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(251, 191, 36, 0.4);
    color: white;
}

.btn-danger-modern {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

.btn-danger-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
    color: white;
}

/* ========================================
   EMPTY STATE
======================================== */
.empty-state {
    padding: 3rem 2rem;
}

.empty-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto 1.5rem;
    background: linear-gradient(135deg, #f7fafc 0%, #e2e8f0 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: float 3s ease-in-out infinite;
}

.empty-icon i {
    font-size: 3rem;
    color: #a0aec0;
}

.empty-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #4a5568;
    margin-bottom: 0.5rem;
}

.empty-subtitle {
    color: #a0aec0;
    font-size: 0.95rem;
    margin-bottom: 0;
}

/* ========================================
   MODERN MODAL
======================================== */
.modern-modal .modal-content {
    border: none;
    border-radius: 16px;
    overflow: hidden;
}

.modal-header-modern {
    padding: 1.5rem 2rem;
    border: none;
}

.modal-header-modern.bg-warning {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
}

.modal-header-modern.bg-danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.modal-icon {
    width: 45px;
    height: 45px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    backdrop-filter: blur(10px);
}

.modal-icon i {
    color: white;
    font-size: 1.35rem;
}

.modal-title {
    color: white;
    font-weight: 700;
    font-size: 1.15rem;
}

.modal-subtitle {
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.85rem;
}

.modal-body-modern {
    padding: 2rem;
}

.form-group-modern {
    margin-bottom: 1.5rem;
}

.form-label-modern {
    font-size: 0.95rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
}

.form-control-modern {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    resize: vertical;
}

.form-control-modern:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.char-counter-edit {
    color: #718096;
    font-size: 0.85rem;
    font-weight: 500;
}

.modal-footer-modern {
    padding: 1.25rem 2rem;
    background: #f7fafc;
    border: none;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}

.btn-cancel {
    padding: 0.625rem 1.25rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-cancel:hover {
    background: #e2e8f0;
    transform: translateY(-2px);
}

.alert-warning-custom {
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-radius: 10px;
    padding: 1rem;
    color: #856404;
    font-size: 0.95rem;
}

/* ========================================
   ANIMATIONS
======================================== */
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

/* ========================================
   RESPONSIVE - TABLET
======================================== */
@media (max-width: 992px) {
    .page-title {
        font-size: 1.5rem;
    }
    
    .icon-wrapper {
        width: 50px;
        height: 50px;
    }
    
    .icon-wrapper i {
        font-size: 1.5rem;
    }
}

/* ========================================
   RESPONSIVE - MOBILE
======================================== */
@media (max-width: 768px) {
    .header-title-group {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .page-header-animated .d-flex {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
    .btn-add {
        width: 100%;
        justify-content: center;
    }
    
    .page-title {
        font-size: 1.35rem;
    }
    
    .icon-wrapper {
        width: 45px;
        height: 45px;
    }
    
    .icon-wrapper i {
        font-size: 1.35rem;
    }
    
    .card-header-custom {
        padding: 1.25rem 1.5rem;
    }
    
    .card-header-custom .d-flex {
        flex-direction: column;
        gap: 1rem;
    }
    
    .search-box {
        width: 100%;
    }
    
    .table-modern thead th,
    .table-modern tbody td {
        padding: 1rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn-warning-modern,
    .btn-danger-modern {
        width: 100%;
        justify-content: center;
    }
    
    .btn-text {
        display: inline;
    }
    
    .modal-header-modern {
        padding: 1.25rem 1.5rem;
    }
    
    .modal-body-modern {
        padding: 1.5rem;
    }
    
    .modal-footer-modern {
        flex-direction: column-reverse;
        padding: 1.25rem 1.5rem;
    }
    
    .modal-footer-modern button {
        width: 100%;
    }
}

/* ========================================
   RESPONSIVE - SMALL MOBILE
======================================== */
@media (max-width: 576px) {
    .page-title {
        font-size: 1.2rem;
    }
    
    .stat-card {
        padding: 1rem;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
    }
    
    .stat-icon i {
        font-size: 1.5rem;
    }
    
    .stat-value {
        font-size: 1.5rem;
    }
    
    .stat-label {
        font-size: 0.85rem;
    }
    
    .table-modern thead th {
        font-size: 0.75rem;
        padding: 0.875rem 0.75rem;
    }
    
    .table-modern tbody td {
        padding: 0.875rem 0.75rem;
        font-size: 0.85rem;
    }
    
    .btn-warning-modern,
    .btn-danger-modern {
        padding: 0.45rem 0.75rem;
        font-size: 0.8rem;
    }
}

/* ========================================
   REDUCED MOTION
======================================== */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.news-row');
            
            rows.forEach(row => {
                const message = row.querySelector('.message-text').textContent.toLowerCase();
                if (message.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Handle delete modal
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const url = button.getAttribute('data-bs-url');
            const form = document.getElementById('deleteForm');
            if (form && url) form.action = url;
        });
    }

    // Handle edit modal
    const editModal = document.getElementById('editModal');
    if (editModal) {
        const editForm = document.getElementById('editForm');
        const editMessage = document.getElementById('editMessage');
        const editCharCount = document.getElementById('editCharCount');

        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const url = button.getAttribute('data-bs-url');
            const newsJson = button.getAttribute('data-news');

            // Parse news data
            let news = null;
            try {
                news = newsJson ? JSON.parse(newsJson) : null;
            } catch (e) {
                console.warn('Failed to parse news data for edit modal', e);
            }

            // Set form action
            if (editForm && url) editForm.action = url;
            
            // Populate textarea
            if (editMessage && news && news.message !== undefined) {
                editMessage.value = news.message;
                // Update character count
                if (editCharCount) {
                    editCharCount.textContent = news.message.length;
                }
            }
        });

        // Character counter for edit modal
        if (editMessage && editCharCount) {
            editMessage.addEventListener('input', function() {
                editCharCount.textContent = this.value.length;
            });
        }

        // Clear form when modal hidden
        editModal.addEventListener('hidden.bs.modal', function() {
            if (editMessage) editMessage.value = '';
            if (editCharCount) editCharCount.textContent = '0';
            if (editForm) editForm.action = '';
        });
    }

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-success-modern');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>
@endsection