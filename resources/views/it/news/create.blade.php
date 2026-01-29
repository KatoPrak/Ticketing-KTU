@extends('layouts.it')

@section('title', 'Add New News')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            {{-- Header Section --}}
            <div class="page-header-animated mb-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="header-title-group">
                        <div class="icon-wrapper">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <div>
                            <h2 class="page-title mb-1">Create New Announcement</h2>
                            <p class="page-subtitle mb-0">Share important updates with your team</p>
                        </div>
                    </div>
                    <a href="{{ route('it.news.index') }}" class="btn btn-outline-secondary btn-back">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>

            {{-- Form Card --}}
            <div class="card border-0 shadow-lg form-card">
                <div class="card-body p-0">
                    <form action="{{ route('it.news.store') }}" method="POST" id="newsForm">
                        @csrf
                        
                        {{-- Form Header --}}
                        <div class="form-header">
                            <div class="form-header-icon">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Announcement Details</h5>
                                <p class="text-muted small mb-0">Fill in the information below</p>
                            </div>
                        </div>

                        {{-- Form Body --}}
                        <div class="form-body">
                            <div class="form-group-modern">
                                <label for="message" class="form-label-modern">
                                    <i class="fas fa-comment-alt me-2"></i>Announcement Message
                                    <span class="text-danger">*</span>
                                </label>
                                
                                <div class="textarea-wrapper">
                                    <textarea 
                                        class="form-control-modern @error('message') is-invalid @enderror" 
                                        id="message" 
                                        name="message" 
                                        rows="6" 
                                        placeholder="Type your announcement message here..."
                                        required
                                        maxlength="1000">{{ old('message') }}</textarea>
                                    
                                    <div class="textarea-footer">
                                        <div class="character-count">
                                            <i class="fas fa-keyboard me-1"></i>
                                            <span id="charCount">0</span> / 1000 characters
                                        </div>
                                        <div class="textarea-hint">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Be clear and concise
                                        </div>
                                    </div>
                                </div>
                                
                                @error('message')
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror

                                {{-- Preview Box --}}
                                <div class="preview-box" id="previewBox">
                                    <div class="preview-header">
                                        <i class="fas fa-eye me-2"></i>Live Preview
                                    </div>
                                    <div class="preview-content">
                                        <div class="preview-item">
                                            <div class="preview-icon">
                                                <i class="fas fa-info-circle"></i>
                                            </div>
                                            <div class="preview-text" id="previewText">
                                                Your announcement will appear here...
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Form Footer --}}
                        <div class="form-footer">
                            <button type="button" class="btn btn-light btn-cancel" onclick="window.history.back()">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-primary btn-submit" id="submitBtn">
                                <span class="btn-text">
                                    <i class="fas fa-paper-plane me-2"></i>Publish Announcement
                                </span>
                                <span class="btn-loading d-none">
                                    <span class="spinner-border spinner-border-sm me-2"></span>Publishing...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Helper Cards --}}
            <div class="row mt-4">
                <div class="col-md-6 mb-3">
                    <div class="helper-card">
                        <div class="helper-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div class="helper-content">
                            <h6 class="helper-title">Tips for Good Announcements</h6>
                            <ul class="helper-list">
                                <li>Keep it brief and to the point</li>
                                <li>Use clear and simple language</li>
                                <li>Include relevant dates or deadlines</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="helper-card">
                        <div class="helper-icon bg-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="helper-content">
                            <h6 class="helper-title">What Makes It Effective?</h6>
                            <ul class="helper-list">
                                <li>Clear call-to-action if needed</li>
                                <li>Professional and friendly tone</li>
                                <li>Important information highlighted</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ========================================
   PAGE HEADER ANIMATION
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

.btn-back {
    padding: 0.625rem 1.25rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: 2px solid #e2e8f0;
}

.btn-back:hover {
    background: #f7fafc;
    border-color: #cbd5e0;
    transform: translateX(-5px);
}

/* ========================================
   FORM CARD
======================================== */
.form-card {
    border-radius: 20px;
    overflow: hidden;
    animation: slideInUp 0.6s ease-out;
}

/* Form Header */
.form-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    position: relative;
    overflow: hidden;
}

.form-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    animation: pulse 4s ease-in-out infinite;
}

.form-header-icon {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
    flex-shrink: 0;
}

.form-header-icon i {
    font-size: 1.5rem;
    color: white;
}

.form-header h5 {
    color: white;
    font-weight: 700;
    margin: 0;
}

.form-header p {
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
}

/* Form Body */
.form-body {
    padding: 2.5rem;
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

.form-label-modern i {
    color: #667eea;
}

/* Textarea Wrapper */
.textarea-wrapper {
    position: relative;
}

.form-control-modern {
    width: 100%;
    padding: 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    resize: vertical;
    font-family: inherit;
}

.form-control-modern:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.form-control-modern::placeholder {
    color: #a0aec0;
}

.textarea-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.5rem;
    font-size: 0.85rem;
}

.character-count {
    color: #718096;
    font-weight: 500;
}

.character-count.warning {
    color: #f59e0b;
}

.character-count.danger {
    color: #ef4444;
}

.textarea-hint {
    color: #a0aec0;
}

/* Preview Box */
.preview-box {
    margin-top: 1.5rem;
    border: 2px dashed #e2e8f0;
    border-radius: 12px;
    padding: 1.25rem;
    background: #f7fafc;
    transition: all 0.3s ease;
}

.preview-header {
    font-size: 0.9rem;
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.preview-content {
    background: white;
    border-radius: 10px;
    padding: 1rem;
}

.preview-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.preview-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #8196ffff 0%, #667ce7 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.preview-icon i {
    color: white;
    font-size: 1.1rem;
}

.preview-text {
    flex: 1;
    color: #4a5568;
    font-size: 0.95rem;
    line-height: 1.6;
    min-height: 50px;
    display: flex;
    align-items: center;
}

.preview-text.empty {
    color: #a0aec0;
    font-style: italic;
}

/* Form Footer */
.form-footer {
    padding: 1.5rem 2.5rem;
    background: #f7fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

.btn-cancel {
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-cancel:hover {
    background: #e2e8f0;
    transform: translateY(-2px);
}

.btn-submit {
    padding: 0.75rem 1.75rem;
    border-radius: 10px;
    font-weight: 600;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-submit::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.btn-submit:hover::before {
    width: 300px;
    height: 300px;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
}

.btn-submit:active {
    transform: translateY(0);
}

/* ========================================
   HELPER CARDS
======================================== */
.helper-card {
    background: white;
    border-radius: 12px;
    padding: 1.25rem;
    border: 1px solid #e2e8f0;
    display: flex;
    gap: 1rem;
    transition: all 0.3s ease;
    animation: fadeIn 0.8s ease-out;
}

.helper-card:hover {
    border-color: #cbd5e0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    transform: translateY(-2px);
}

.helper-icon {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.helper-icon.bg-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.helper-icon i {
    color: white;
    font-size: 1.25rem;
}

.helper-content {
    flex: 1;
}

.helper-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.helper-list {
    margin: 0;
    padding-left: 1.25rem;
    font-size: 0.85rem;
    color: #718096;
}

.helper-list li {
    margin-bottom: 0.25rem;
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

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
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

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        opacity: 0.5;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.8;
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
    
    .form-body {
        padding: 2rem;
    }
    
    .form-footer {
        padding: 1.25rem 2rem;
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
    
    .btn-back {
        width: 100%;
        text-align: center;
    }
    
    .page-title {
        font-size: 1.35rem;
    }
    
    .page-subtitle {
        font-size: 0.9rem;
    }
    
    .icon-wrapper {
        width: 45px;
        height: 45px;
    }
    
    .icon-wrapper i {
        font-size: 1.35rem;
    }
    
    .form-header {
        padding: 1.5rem;
        flex-direction: column;
        text-align: center;
    }
    
    .form-body {
        padding: 1.5rem;
    }
    
    .form-footer {
        padding: 1.25rem 1.5rem;
        flex-direction: column-reverse;
    }
    
    .btn-cancel,
    .btn-submit {
        width: 100%;
    }
    
    .textarea-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .helper-card {
        flex-direction: column;
        text-align: center;
    }
    
    .helper-icon {
        margin: 0 auto;
    }
}

/* ========================================
   RESPONSIVE - SMALL MOBILE
======================================== */
@media (max-width: 576px) {
    .page-title {
        font-size: 1.2rem;
    }
    
    .icon-wrapper {
        width: 40px;
        height: 40px;
    }
    
    .icon-wrapper i {
        font-size: 1.2rem;
    }
    
    .form-header {
        padding: 1.25rem;
    }
    
    .form-header-icon {
        width: 45px;
        height: 45px;
    }
    
    .form-header-icon i {
        font-size: 1.35rem;
    }
    
    .form-body {
        padding: 1.25rem;
    }
    
    .form-control-modern {
        font-size: 0.9rem;
        padding: 0.875rem;
    }
    
    .preview-box {
        padding: 1rem;
    }
    
    .helper-card {
        padding: 1rem;
    }
    
    .helper-list {
        font-size: 0.8rem;
    }
}

/* ========================================
   EXTRA SMALL MOBILE
======================================== */
@media (max-width: 400px) {
    .page-title {
        font-size: 1.1rem;
    }
    
    .page-subtitle {
        font-size: 0.85rem;
    }
    
    .form-header h5 {
        font-size: 1rem;
    }
    
    .form-header p {
        font-size: 0.85rem;
    }
    
    .form-label-modern {
        font-size: 0.9rem;
    }
    
    .btn-submit,
    .btn-cancel {
        font-size: 0.9rem;
        padding: 0.65rem 1.25rem;
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
    const messageInput = document.getElementById('message');
    const charCount = document.getElementById('charCount');
    const previewText = document.getElementById('previewText');
    const submitBtn = document.getElementById('submitBtn');
    const newsForm = document.getElementById('newsForm');

    // Character counter and live preview
    if (messageInput) {
        messageInput.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length;
            
            // Update character count color
            charCount.classList.remove('warning', 'danger');
            if (length > 400) {
                charCount.classList.add('danger');
            } else if (length > 300) {
                charCount.classList.add('warning');
            }
            
            // Update preview
            if (this.value.trim()) {
                previewText.textContent = this.value;
                previewText.classList.remove('empty');
            } else {
                previewText.textContent = 'Your announcement will appear here...';
                previewText.classList.add('empty');
            }
        });
        
        // Trigger initial update
        messageInput.dispatchEvent(new Event('input'));
    }

    // Form submission animation
    if (newsForm) {
        newsForm.addEventListener('submit', function(e) {
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');
            
            btnText.classList.add('d-none');
            btnLoading.classList.remove('d-none');
            submitBtn.disabled = true;
        });
    }

    // Auto-grow textarea
    if (messageInput) {
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }
});
</script>
@endsection