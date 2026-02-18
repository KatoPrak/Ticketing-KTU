@extends('layouts.staff')

@section('title', 'Staff Dashboard')

@section('body-class', 'page-staff-dashboard')

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
<div style="zoom: 90%;">
<div class="welcome-banner-animated">
    <div class="background-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    
    <div class="welcome-content">
        <div class="row align-items-center">
            {{-- Left Side: Greeting & Info --}}
            <div class="col-md-8 col-12">
                <div class="greeting-section">
                    <h2 class="greeting-title">
                        <span class="wave-hand">👋</span>
                        <span class="greeting-hi">Hi!</span>
                        <strong class="user-name-highlight">{{ Auth::user()->name }}</strong>
                        <span class="greeting-question">Need Help?</span>
                    </h2>
                </div>
                
                <div class="info-section">
                    <div class="department-info">
                        <i class="fas fa-building icon-pulse"></i>
                        <span class="department-label">Department:</span>
                        <span class="badge bg-light text-primary department-badge">
                            {{ Auth::user()->department ? Auth::user()->department->name : '-' }}
                        </span>
                    </div>
                    
                    <div class="datetime-info">
                        <div class="datetime-box">
                            <i class="fas fa-calendar-day"></i>
                            <span id="currentDate"></span>
                            <span class="divider">•</span>
                            <i class="fas fa-clock clock-tick"></i>
                            <span id="currentTime"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Side: User Icon --}}
            <div class="col-md-4 col-12 text-center text-md-end mt-3 mt-md-0">
                <div class="user-icon-wrapper">
                    <i class="fas fa-user-tie user-icon"></i>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
/* ========================================
   WELCOME BANNER STYLING
======================================== */
.welcome-banner-animated {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.35);
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
    -webkit-transform: translateZ(0);
    transform: translateZ(0);
}

/* Background Animated Shapes */
.background-shapes {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 0;
    pointer-events: none;
}

.shape {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    will-change: transform;
}

.shape-1 {
    width: 200px;
    height: 200px;
    top: -70px;
    right: -40px;
    animation: float 8s ease-in-out infinite;
}

.shape-2 {
    width: 140px;
    height: 140px;
    bottom: -50px;
    left: -25px;
    animation: float 6s ease-in-out infinite reverse;
}

.shape-3 {
    width: 100px;
    height: 100px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    animation: pulse 4s ease-in-out infinite;
}

/* Content Wrapper */
.welcome-content {
    position: relative;
    z-index: 1;
    color: white;
}

/* Greeting Section */
.greeting-section {
    margin-bottom: 1rem;
    animation: fadeInDown 0.8s ease-out;
}

.greeting-title {
    margin: 0;
    line-height: 1.4;
    font-size: 1.8rem;
}

.wave-hand {
    display: inline-block;
    font-size: 2rem;
    animation: wave 2s ease-in-out infinite;
    transform-origin: 70% 70%;
    margin-right: 0.5rem;
}

.greeting-hi {
    font-weight: 400;
    color: rgba(255, 255, 255, 0.95);
    animation: fadeIn 0.8s ease-out 0.2s both;
}

.user-name-highlight {
    display: inline-block;
    font-weight: 700;
    color: #ffd700;
    animation: slideInLeft 0.8s ease-out 0.4s both;
    background: linear-gradient(90deg, #ffd700, #ffd700);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0 0.3rem;
}

.greeting-question {
    font-weight: 400;
    color: rgba(255, 255, 255, 0.95);
    animation: fadeIn 0.8s ease-out 0.6s both;
}

/* Info Section */
.info-section {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: center;
    animation: fadeIn 1s ease-out 0.8s both;
}

/* Department Info */
.department-info {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    padding: 0.6rem 1rem;
    border-radius: 50px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    font-size: 0.9rem;
    animation: slideInUp 0.8s ease-out 1s both;
}

.department-label {
    font-weight: 500;
    color: rgba(255, 255, 255, 0.9);
}

.department-badge {
    padding: 0.4rem 0.8rem;
    font-weight: 600;
    font-size: 0.85rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    animation: bounce 1s ease-out 1.5s;
}

.icon-pulse {
    color: #ffd700;
    font-size: 0.9rem;
    animation: pulse 2s ease-in-out infinite;
}

/* DateTime Info */
.datetime-info {
    animation: slideInUp 0.8s ease-out 1.2s both;
}

.datetime-box {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    padding: 0.6rem 1rem;
    border-radius: 50px;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    font-size: 0.85rem;
    font-weight: 500;
    white-space: nowrap;
}

.datetime-box i {
    color: #ffd700;
    font-size: 0.85rem;
}

.divider {
    color: rgba(255, 255, 255, 0.5);
    margin: 0 0.2rem;
}

.clock-tick {
    animation: tickTock 1s ease-in-out infinite;
}

/* ========================================
   USER ICON STYLING
======================================== */
.user-icon-wrapper {
    animation: fadeIn 1s ease-out 0.5s both;
}

.user-icon {
    font-size: 4rem;
    color: rgba(255, 255, 255, 0.9);
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
    animation: float 3s ease-in-out infinite;
    transition: all 0.3s ease;
}

.user-icon:hover {
    transform: scale(1.1);
    color: #ffd700;
}

/* ========================================
   LATEST TICKETS TABLE STYLING
======================================== */
.icon-wrapper {
    transition: all 0.3s ease;
}

.icon-wrapper:hover {
    transform: scale(1.05);
}

.table tbody tr {
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    background-color: rgba(var(--bs-primary-rgb), 0.05);
    transform: translateX(5px);
}

.table th {
    font-weight: 600;
    letter-spacing: 0.5px;
}

/* ========================================
   KEYFRAME ANIMATIONS
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

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes wave {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(-20deg); }
    75% { transform: rotate(20deg); }
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
        opacity: 0.8;
    }
    50% {
        transform: scale(1.08);
        opacity: 1;
    }
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    25% {
        transform: translateY(-4px);
    }
    50% {
        transform: translateY(0);
    }
    75% {
        transform: translateY(-2px);
    }
}

@keyframes tickTock {
    0%, 100% {
        transform: rotate(0deg);
    }
    25% {
        transform: rotate(-5deg);
    }
    75% {
        transform: rotate(5deg);
    }
}

/* ========================================
   RESPONSIVE - TABLET
======================================== */
@media (max-width: 992px) {
    .welcome-banner-animated {
        padding: 1.25rem 1.5rem;
    }
    
    .greeting-title {
        font-size: 1.5rem;
    }
    
    .wave-hand {
        font-size: 1.6rem;
    }
    
    .user-icon {
        font-size: 3.5rem;
    }
}

/* ========================================
   RESPONSIVE - MOBILE & iOS
======================================== */
@media (max-width: 768px) {
    .welcome-banner-animated {
        padding: 1.25rem;
        border-radius: 12px;
    }
    
    .greeting-title {
        font-size: 1.3rem;
        line-height: 1.5;
    }
    
    .wave-hand {
        font-size: 1.5rem;
    }
    
    .user-name-highlight {
        display: block;
        margin: 0.25rem 0;
    }
    
    .info-section {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .department-info,
    .datetime-box {
        font-size: 0.8rem;
        padding: 0.5rem 0.85rem;
    }
    
    .department-badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
    }
    
    .user-icon {
        font-size: 3rem;
    }
    
    .shape-1 {
        width: 150px;
        height: 150px;
        top: -55px;
        right: -30px;
    }
    
    .shape-2 {
        width: 110px;
        height: 110px;
        bottom: -40px;
        left: -20px;
    }
    
    .shape-3 {
        width: 80px;
        height: 80px;
    }
}

/* ========================================
   iOS SPECIFIC OPTIMIZATIONS
======================================== */
@supports (-webkit-touch-callout: none) {
    .welcome-banner-animated {
        -webkit-transform: translate3d(0, 0, 0);
        transform: translate3d(0, 0, 0);
    }
    
    .background-shapes,
    .shape {
        -webkit-transform: translateZ(0);
        transform: translateZ(0);
    }
}

/* iPhone SE & Small devices */
@media (max-width: 390px) {
    .welcome-banner-animated {
        padding: 1rem;
    }
    
    .greeting-title {
        font-size: 1.1rem;
    }
    
    .wave-hand {
        font-size: 1.3rem;
    }
    
    .user-name-highlight {
        font-size: 1.2rem;
    }
    
    .info-section {
        width: 100%;
    }
    
    .department-info {
        width: 100%;
        justify-content: flex-start;
        font-size: 0.75rem;
        padding: 0.45rem 0.75rem;
    }
    
    .department-label {
        font-size: 0.75rem;
    }
    
    .department-badge {
        font-size: 0.7rem;
        padding: 0.3rem 0.55rem;
    }
    
    .datetime-info {
        width: 100%;
    }
    
    .datetime-box {
        width: 100%;
        justify-content: flex-start;
        font-size: 0.75rem;
        padding: 0.45rem 0.75rem;
    }
    
    .datetime-box i {
        font-size: 0.75rem;
    }
    
    .user-icon {
        font-size: 2.5rem;
    }
}

/* Landscape mode */
@media (max-height: 500px) and (orientation: landscape) {
    .welcome-banner-animated {
        padding: 0.75rem 1rem;
    }
    
    .greeting-section {
        margin-bottom: 0.5rem;
    }
    
    .greeting-title {
        font-size: 1.1rem;
        line-height: 1.3;
    }
    
    .wave-hand {
        font-size: 1.2rem;
    }
    
    .info-section {
        flex-direction: row;
        gap: 0.75rem;
    }
    
    .user-icon {
        font-size: 2.5rem;
    }
}

/* iPad & iPad Pro */
@media (min-width: 768px) and (max-width: 1024px) {
    .welcome-banner-animated {
        padding: 1.5rem 2rem;
    }
    
    .greeting-title {
        font-size: 1.6rem;
    }
    
    .user-icon {
        font-size: 3.5rem;
    }
}

/* Reduce motion for accessibility */
@media (prefers-reduced-motion: reduce) {
    .wave-hand,
    .icon-pulse,
    .clock-tick,
    .shape,
    .user-icon {
        animation: none !important;
    }
    
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Rest of Quick Actions styles remain the same... */
</style>

<script>
// ========================================
// REAL-TIME CLOCK & DATE - iOS Compatible
// ========================================
function updateDateTime() {
    const now = new Date();
    
    // Format tanggal: Nov 02, 2025
    const dateOptions = { 
        month: 'short', 
        day: '2-digit', 
        year: 'numeric' 
    };
    const formattedDate = now.toLocaleDateString('en-US', dateOptions);
    
    // Format waktu: 14:30:45
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    const formattedTime = `${hours}:${minutes}:${seconds}`;
    
    // Update DOM safely
    const dateElement = document.getElementById('currentDate');
    const timeElement = document.getElementById('currentTime');
    
    if (dateElement) dateElement.textContent = formattedDate;
    if (timeElement) timeElement.textContent = formattedTime;
}

// iOS-safe initialization
document.addEventListener('DOMContentLoaded', function() {
    // Initial update
    updateDateTime();
    
    // Update every second
    const intervalId = setInterval(updateDateTime, 1000);
    
    // Handle visibility change (iOS background handling)
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            updateDateTime();
        }
    });
    
    // Handle page unload
    window.addEventListener('beforeunload', function() {
        clearInterval(intervalId);
    });
});

// Handle iOS orientation change
if (window.orientation !== undefined) {
    window.addEventListener('orientationchange', function() {
        setTimeout(updateDateTime, 100);
    });
}
</script>

{{-- Improved Announcements & News Section --}}
<div class="card border-0 shadow-sm mb-4 announcement-card">
    <div class="card-body p-4">
        {{-- Header Section --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <div class="announcement-icon-wrapper bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                    <i class="fas fa-bullhorn text-warning fs-4"></i>
                </div>
                <div>
                    <h5 class="mb-1 fw-bold text-dark announcement-title">Announcements & News</h5>
                    <p class="text-muted mb-0 small">Stay updated with latest information</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($news->count() > 0)
                <span class="badge bg-warning text-dark announcement-badge">
                    {{ $news->count() }} {{ $news->count() > 1 ? 'Updates' : 'Update' }}
                </span>
                @endif
                <button class="btn btn-sm btn-outline-secondary toggle-announcements-btn collapsed" type="button" title="Hide/Show Announcements">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
        </div>

        {{-- Announcements List --}}
        <div id="announcementsContainer" class="announcements-wrapper collapsed">
        @if($news->count() > 0)
            <div class="announcements-container">
                @foreach($news as $index => $item)
                <div class="announcement-item" style="animation-delay: {{ $index * 0.1 }}s;">
                    <div class="announcement-icon-box">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="announcement-content">
                        @php
                            $message = $item->message;
                            $maxLength = 150; // Maximum characters before truncate
                            $isLong = strlen($message) > $maxLength;
                            $shortMessage = $isLong ? substr($message, 0, $maxLength) . '...' : $message;
                        @endphp
                        
                        <h6 class="announcement-message">
                            <span class="message-short" data-full="{{ $message }}" data-short="{{ $shortMessage }}">
                                {{ $shortMessage }}
                            </span>
                        </h6>
                        
                        @if($isLong)
                        <button class="btn btn-link btn-sm p-0 text-primary read-more-btn" style="font-size: 0.85rem; text-decoration: none;">
                            <i class="fas fa-chevron-down me-1"></i>Read More
                        </button>
                        @endif
                        
                        <div class="announcement-meta mt-2">
                            <i class="far fa-clock me-1"></i>
                            <span>{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="announcement-indicator"></div>
                </div>
                @endforeach
            </div>
        @else
            {{-- Empty State --}}
            <div class="empty-announcement-state">
                <div class="empty-icon-wrapper">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <h6 class="empty-title">No Announcements Yet</h6>
                <p class="empty-subtitle">There are no news or announcements at the moment.</p>
            </div>
        @endif
        </div>{{-- End announcements-wrapper --}}
    </div>
</div>

<style>
/* ========================================
   ANNOUNCEMENTS CARD STYLING
======================================== */
.announcement-card {
    border-radius: 16px;
    transition: all 0.3s ease;
    overflow: hidden;
}

.announcement-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1) !important;
}

/* Header Icon Wrapper */
.announcement-icon-wrapper {
    transition: all 0.3s ease;
}

.announcement-icon-wrapper:hover {
    transform: scale(1.05);
}

/* Title */
.announcement-title {
    font-size: 1.25rem;
    transition: font-size 0.3s ease;
}

/* Badge */
.announcement-badge {
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.85rem;
    animation: pulseScale 2s ease-in-out infinite;
}

/* ========================================
   ANNOUNCEMENTS CONTAINER
======================================== */
.announcements-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

/* ========================================
   ANNOUNCEMENT ITEM
======================================== */
.announcement-item {
    display: flex;
    align-items: flex-start;
    padding: 1.25rem;
    background: linear-gradient(135deg, #fffbea 0%, #fff8dc 100%);
    border: 1px solid #879bffff;
    border-left: 4px solid #667ce7;
    border-radius: 12px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    animation: slideInUp 0.6s ease-out backwards;
}

.announcement-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transform: translateX(-100%);
    transition: transform 0.6s ease;
}

.announcement-item:hover::before {
    transform: translateX(100%);
}

.announcement-item:hover {
    transform: translateX(5px);
    box-shadow: 0 8px 16px rgba(255, 193, 7, 0.2);
    border-left-width: 6px;
}

/* Icon Box */
.announcement-icon-box {
    flex-shrink: 0;
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #899dffff 0%, #667ce7 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
}

.announcement-icon-box i {
    color: white;
    font-size: 1.25rem;
    animation: pulseIcon 2s ease-in-out infinite;
}

.announcement-item:hover .announcement-icon-box {
    transform: rotate(10deg) scale(1.1);
    box-shadow: 0 6px 16px rgba(255, 193, 7, 0.4);
}

/* Content */
.announcement-content {
    flex: 1;
    min-width: 0;
}

.announcement-message {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: #2d3748;
    line-height: 1.5;
    word-wrap: break-word;
    transition: color 0.3s ease;
}

.announcement-item:hover .announcement-message {
    color: #1a202c;
}

.announcement-meta {
    display: flex;
    align-items: center;
    font-size: 0.85rem;
    color: #718096;
    gap: 0.25rem;
}

.announcement-meta i {
    font-size: 0.8rem;
}

/* Indicator */
.announcement-indicator {
    position: absolute;
    top: 50%;
    right: 1rem;
    width: 8px;
    height: 8px;
    background: #7b8de7ff;
    border-radius: 50%;
    transform: translateY(-50%);
    animation: blink 2s ease-in-out infinite;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.announcement-item:hover .announcement-indicator {
    opacity: 1;
}

/* ========================================
   EMPTY STATE
======================================== */
.empty-announcement-state {
    text-align: center;
    padding: 3rem 2rem;
}

.empty-icon-wrapper {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: float 3s ease-in-out infinite;
}

.empty-icon-wrapper i {
    font-size: 2.5rem;
    color: #9ca3af;
    opacity: 0.5;
}

.empty-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #4b5563;
    margin-bottom: 0.5rem;
}

.empty-subtitle {
    font-size: 0.9rem;
    color: #9ca3af;
    margin: 0;
}

/* ========================================
   KEYFRAME ANIMATIONS
======================================== */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulseScale {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

@keyframes pulseIcon {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

@keyframes blink {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.3;
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
   READ MORE BUTTON
======================================== */
.read-more-btn {
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    background: none;
    cursor: pointer;
}

.read-more-btn:hover {
    transform: translateX(3px);
    color: #0056b3 !important;
}

.read-more-btn:focus {
    outline: none;
    box-shadow: none;
}

.read-more-btn i {
    transition: transform 0.3s ease;
}

.read-more-btn.expanded i {
    transform: rotate(180deg);
}

.message-short {
    display: inline;
    transition: all 0.3s ease;
}

/* ========================================
   TOGGLE ANNOUNCEMENTS BUTTON
======================================== */
.toggle-announcements-btn {
    border-radius: 8px;
    padding: 0.4rem 0.6rem;
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
}

.toggle-announcements-btn:hover {
    background-color: #f8f9fa;
    border-color: #adb5bd;
    transform: scale(1.05);
}

.toggle-announcements-btn i {
    transition: transform 0.3s ease;
    font-size: 0.9rem;
}

.toggle-announcements-btn.collapsed i {
    transform: rotate(180deg);
}

.announcements-wrapper {
    overflow: hidden;
    transition: max-height 0.4s ease, opacity 0.3s ease;
    max-height: 2000px;
    opacity: 1;
}

.announcements-wrapper.collapsed {
    max-height: 0;
    opacity: 0;
    margin-bottom: 0 !important;
}

/* ========================================
   RESPONSIVE - DESKTOP
======================================== */
@media (min-width: 1400px) {
    .announcement-title {
        font-size: 1.35rem;
    }
    
    .announcement-message {
        font-size: 1.05rem;
    }
    
    .announcement-icon-box {
        width: 50px;
        height: 50px;
    }
}

/* ========================================
   RESPONSIVE - TABLET
======================================== */
@media (max-width: 992px) {
    .announcement-title {
        font-size: 1.15rem;
    }
    
    .announcement-badge {
        font-size: 0.8rem;
        padding: 0.4rem 0.85rem;
    }
    
    .announcement-item {
        padding: 1rem;
    }
    
    .announcement-icon-box {
        width: 40px;
        height: 40px;
    }
    
    .announcement-icon-box i {
        font-size: 1.1rem;
    }
    
    .announcement-message {
        font-size: 0.95rem;
    }
    
    .announcement-meta {
        font-size: 0.8rem;
    }
}

/* ========================================
   RESPONSIVE - MOBILE
======================================== */
@media (max-width: 768px) {
    .card-body {
        padding: 1.25rem !important;
    }
    
    .announcement-title {
        font-size: 1.1rem;
    }
    
    .announcement-badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.75rem;
    }
    
    .announcement-item {
        padding: 0.9rem;
        flex-direction: column;
        align-items: flex-start;
    }
    
    .announcement-icon-box {
        width: 38px;
        height: 38px;
        margin-right: 0;
        margin-bottom: 0.75rem;
    }
    
    .announcement-icon-box i {
        font-size: 1rem;
    }
    
    .announcement-message {
        font-size: 0.9rem;
        margin-bottom: 0.4rem;
    }
    
    .announcement-meta {
        font-size: 0.75rem;
    }
    
    .announcement-indicator {
        top: 1rem;
        right: 0.75rem;
        transform: none;
    }
    
    .empty-announcement-state {
        padding: 2rem 1rem;
    }
    
    .empty-icon-wrapper {
        width: 65px;
        height: 65px;
        margin-bottom: 1rem;
    }
    
    .empty-icon-wrapper i {
        font-size: 2rem;
    }
    
    .empty-title {
        font-size: 1rem;
    }
    
    .empty-subtitle {
        font-size: 0.85rem;
    }
}

/* ========================================
   RESPONSIVE - SMALL MOBILE
======================================== */
@media (max-width: 576px) {
    .announcement-title {
        font-size: 1rem;
    }
    
    .announcement-icon-wrapper p {
        display: none;
    }
    
    .announcement-badge {
        font-size: 0.7rem;
        padding: 0.3rem 0.65rem;
    }
    
    .announcement-item {
        padding: 0.85rem;
        border-left-width: 3px;
    }
    
    .announcement-icon-box {
        width: 35px;
        height: 35px;
        margin-bottom: 0.65rem;
    }
    
    .announcement-icon-box i {
        font-size: 0.95rem;
    }
    
    .announcement-message {
        font-size: 0.85rem;
    }
    
    .announcement-meta {
        font-size: 0.7rem;
    }
}

/* ========================================
   RESPONSIVE - EXTRA SMALL
======================================== */
@media (max-width: 400px) {
    .card-body {
        padding: 1rem !important;
    }
    
    .announcement-title {
        font-size: 0.95rem;
    }
    
    .announcement-icon-wrapper {
        padding: 0.65rem !important;
    }
    
    .announcement-icon-wrapper .fs-4 {
        font-size: 1.1rem !important;
    }
    
    .announcement-badge {
        font-size: 0.65rem;
        padding: 0.25rem 0.55rem;
    }
    
    .announcement-item {
        padding: 0.75rem;
    }
    
    .announcement-icon-box {
        width: 32px;
        height: 32px;
        margin-bottom: 0.6rem;
    }
    
    .announcement-icon-box i {
        font-size: 0.9rem;
    }
    
    .announcement-message {
        font-size: 0.8rem;
    }
    
    .announcement-meta {
        font-size: 0.65rem;
    }
    
    .empty-icon-wrapper {
        width: 55px;
        height: 55px;
    }
    
    .empty-icon-wrapper i {
        font-size: 1.75rem;
    }
    
    .empty-title {
        font-size: 0.95rem;
    }
    
    .empty-subtitle {
        font-size: 0.8rem;
    }
}

/* ========================================
   LANDSCAPE MODE
======================================== */
@media (max-height: 500px) and (orientation: landscape) {
    .announcement-item {
        padding: 0.7rem;
    }
    
    .announcement-icon-box {
        width: 35px;
        height: 35px;
        margin-bottom: 0.5rem;
    }
    
    .empty-announcement-state {
        padding: 1.5rem 1rem;
    }
    
    .empty-icon-wrapper {
        width: 50px;
        height: 50px;
        margin-bottom: 0.75rem;
    }
}

/* ========================================
   iOS SPECIFIC
======================================== */
@supports (-webkit-touch-callout: none) {
    .announcement-item,
    .announcement-icon-box {
        -webkit-tap-highlight-color: transparent;
        -webkit-font-smoothing: antialiased;
    }
}

/* ========================================
   REDUCED MOTION
======================================== */
@media (prefers-reduced-motion: reduce) {
    .announcement-item,
    .announcement-icon-box,
    .announcement-badge,
    .empty-icon-wrapper,
    .announcement-indicator {
        animation: none !important;
    }
    
    * {
        transition-duration: 0.01ms !important;
    }
}
</style>

{{-- Quick Actions & Latest Tickets --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="row">
                    {{-- Quick Actions --}}
                    <div class="col-lg-4 border-end-lg mb-4 mb-lg-0">
                        <h5 class="mb-3 fw-bold quick-actions-title">
                            <i class="fas fa-bolt text-primary me-2"></i>Quick Actions
                        </h5>
                        <div class="d-grid gap-3">
                            <button type="button" class="btn btn-primary btn-quick-action mb-2" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                                <div class="d-flex align-items-center justify-content-start">
                                    <div class="icon-circle bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                        <i class="fas fa-plus"></i>
                                    </div>
                                    <div class="text-start">
                                        <h6 class="mb-0 text-white action-title">Create New Ticket</h6>
                                        <small class="text-white-50 action-subtitle">Report your issue</small>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>

                    {{-- Latest Tickets --}}
                    <div class="col-lg-8 ps-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                {{-- Header Section --}}
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                                            <i class="fas fa-ticket-alt text-primary fs-4"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1 fw-bold text-dark">Your Latest Tickets</h5>
                                            <p class="text-muted mb-0 small">Track your recent support requests</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('staff.tickets.index') }}" class="btn btn-link text-decoration-none text-primary">
                                        View All <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>

                                {{-- Table Section --}}
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th scope="col" class="border-0 text-uppercase text-muted small fw-semibold py-3">
                                                    <i class="fas fa-hashtag me-2"></i>Ticket ID
                                                </th>
                                                <th scope="col" class="border-0 text-uppercase text-muted small fw-semibold py-3">
                                                    <i class="fas fa-file-alt me-2"></i>Problem
                                                </th>
                                                <th scope="col" class="border-0 text-uppercase text-muted small fw-semibold py-3">
                                                    <i class="fas fa-flag me-2"></i>Priority
                                                </th>
                                                <th scope="col" class="border-0 text-uppercase text-muted small fw-semibold py-3">
                                                    <i class="fas fa-info-circle me-2"></i>Status
                                                </th>
                                                <th scope="col" class="border-0 text-uppercase text-muted small fw-semibold py-3 text-end">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="ticket-list-body">
                                            {{-- Loading State --}}
                                            <tr id="tickets-loading">
                                                <td colspan="5" class="text-center py-5">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    <p class="text-muted mt-3 mb-0">Loading your tickets...</p>
                                                </td>
                                            </tr>
                                            {{-- Filled dynamically by JS --}}
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Empty State --}}
                                <div id="no-tickets-message" class="text-center py-5 d-none">
                                    <i class="fas fa-inbox text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <h6 class="text-muted mb-2">No Tickets Yet</h6>
                                    <p class="text-muted small mb-3">You haven't created any support tickets yet.</p>
                                    <a href="{{ route('staff.tickets.index') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-2"></i>Create Your First Ticket
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ========================================
   QUICK ACTIONS RESPONSIVE STYLING
======================================== */

/* Quick Actions Title */
.quick-actions-title {
    font-size: 1.25rem;
    transition: font-size 0.3s ease;
}

/* Quick Action Button */
.btn-quick-action {
    padding: 1rem 1.25rem;
    border-radius: 12px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

.btn-quick-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
}

.btn-quick-action:active {
    transform: translateY(0);
}

.icon-circle {
    width: 40px;
    height: 40px;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.btn-quick-action-outline {
    padding: 1rem 1.25rem;
    border-radius: 12px;
    transition: all 0.3s ease;
    border: 1px solid #e2e8f0;
    background: white;
    text-decoration: none;
}

.btn-quick-action-outline:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.btn-quick-action:hover .icon-circle,
.btn-quick-action-outline:hover .icon-circle {
    transform: rotate(15deg) scale(1.1);
}

/* Action Text */
.action-title {
    font-size: 1rem;
    font-weight: 600;
    transition: font-size 0.3s ease;
}

.action-subtitle {
    font-size: 0.875rem;
    transition: font-size 0.3s ease;
}

/* Border End for Desktop */
.border-end-lg {
    border-right: 1px solid #e5e7eb;
}

/* ========================================
   RESPONSIVE BREAKPOINTS
======================================== */

/* Large Desktop (1400px+) */
@media (min-width: 1400px) {
    .quick-actions-title {
        font-size: 1.35rem;
    }
    
    .action-title {
        font-size: 1.1rem;
    }
    
    .icon-circle {
        width: 45px;
        height: 45px;
    }
}

/* Desktop (1200px - 1400px) */
@media (max-width: 1200px) {
    .quick-actions-title {
        font-size: 1.2rem;
    }
    
    .action-title {
        font-size: 0.95rem;
    }
    
    .action-subtitle {
        font-size: 0.8rem;
    }
    
    .icon-circle {
        width: 38px;
        height: 38px;
    }
    
    .btn-quick-action {
        padding: 0.9rem 1rem;
    }
}

/* Tablet & Below (< 992px) */
@media (max-width: 992px) {
    /* Remove border-end on tablet */
    .border-end-lg {
        border-right: none;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 1.5rem;
    }
    
    .quick-actions-title {
        font-size: 1.15rem;
    }
    
    .action-title {
        font-size: 0.95rem;
    }
    
    .action-subtitle {
        font-size: 0.8rem;
    }
    
    .icon-circle {
        width: 36px;
        height: 36px;
    }
    
    .btn-quick-action {
        padding: 0.85rem 1rem;
    }
    
    /* Full width button on tablet */
    .btn-quick-action .d-flex {
        justify-content: center !important;
    }
}

/* Mobile Large (768px - 992px) */
@media (max-width: 768px) {
    .quick-actions-title {
        font-size: 1.1rem;
        text-align: center;
    }
    
    .action-title {
        font-size: 0.9rem;
    }
    
    .action-subtitle {
        font-size: 0.75rem;
    }
    
    .icon-circle {
        width: 35px;
        height: 35px;
    }
    
    .icon-circle i {
        font-size: 0.95rem;
    }
    
    .btn-quick-action {
        padding: 0.8rem 0.9rem;
    }
    
    .btn-quick-action .d-flex {
        justify-content: center !important;
        text-align: center;
    }
}

/* Mobile Medium (576px - 768px) */
@media (max-width: 576px) {
    .quick-actions-title {
        font-size: 1rem;
        text-align: center;
        margin-bottom: 1rem !important;
    }
    
    .action-title {
        font-size: 0.85rem;
    }
    
    .action-subtitle {
        font-size: 0.7rem;
    }
    
    .icon-circle {
        width: 32px;
        height: 32px;
    }
    
    .icon-circle i {
        font-size: 0.9rem;
    }
    
    .btn-quick-action {
        padding: 0.75rem 0.85rem;
        border-radius: 10px;
    }
    
    .btn-quick-action .text-start {
        text-align: center !important;
    }
}

/* Mobile Small (< 400px) */
@media (max-width: 400px) {
    .quick-actions-title {
        font-size: 0.95rem;
    }
    
    .quick-actions-title i {
        font-size: 0.9rem;
    }
    
    .action-title {
        font-size: 0.8rem;
    }
    
    .action-subtitle {
        font-size: 0.65rem;
    }
    
    .icon-circle {
        width: 30px;
        height: 30px;
        margin-right: 0.5rem !important;
    }
    
    .icon-circle i {
        font-size: 0.8rem;
    }
    
    .btn-quick-action {
        padding: 0.7rem 0.75rem;
        border-radius: 8px;
    }
}

/* iPhone SE & Extra Small (320px - 375px) */
@media (max-width: 375px) {
    .quick-actions-title {
        font-size: 0.9rem;
    }
    
    .action-title {
        font-size: 0.75rem;
    }
    
    .action-subtitle {
        font-size: 0.6rem;
    }
    
    .icon-circle {
        width: 28px;
        height: 28px;
    }
    
    .icon-circle i {
        font-size: 0.75rem;
    }
    
    .btn-quick-action {
        padding: 0.65rem 0.7rem;
    }
}

/* Landscape Mode for Mobile */
@media (max-height: 500px) and (orientation: landscape) {
    .quick-actions-title {
        font-size: 0.95rem;
        margin-bottom: 0.75rem !important;
    }
    
    .btn-quick-action {
        padding: 0.6rem 0.8rem;
    }
    
    .action-title {
        font-size: 0.85rem;
    }
    
    .action-subtitle {
        font-size: 0.7rem;
    }
    
    .icon-circle {
        width: 30px;
        height: 30px;
    }
}

/* iPad & iPad Pro */
@media (min-width: 768px) and (max-width: 1024px) {
    .quick-actions-title {
        font-size: 1.2rem;
    }
    
    .action-title {
        font-size: 1rem;
    }
    
    .action-subtitle {
        font-size: 0.85rem;
    }
    
    .icon-circle {
        width: 40px;
        height: 40px;
    }
    
    .btn-quick-action {
        padding: 1rem 1.15rem;
    }
}

/* iOS Specific */
@supports (-webkit-touch-callout: none) {
    .btn-quick-action {
        -webkit-tap-highlight-color: transparent;
        -webkit-font-smoothing: antialiased;
    }
}

/* Reduce Motion for Accessibility */
@media (prefers-reduced-motion: reduce) {
    .btn-quick-action,
    .icon-circle,
    .action-title,
    .action-subtitle {
        transition: none !important;
    }
    
    .btn-quick-action:hover .icon-circle {
        transform: none !important;
    }
}
</style>

<script>
// ========================================
// READ MORE / READ LESS FUNCTIONALITY
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    // Read More/Less for news messages
    const readMoreButtons = document.querySelectorAll('.read-more-btn');
    
    readMoreButtons.forEach(button => {
        button.addEventListener('click', function() {
            const messageSpan = this.previousElementSibling.querySelector('.message-short');
            const isExpanded = this.classList.contains('expanded');
            
            if (isExpanded) {
                // Collapse - show short message
                messageSpan.textContent = messageSpan.dataset.short;
                this.innerHTML = '<i class="fas fa-chevron-down me-1"></i>Read More';
                this.classList.remove('expanded');
            } else {
                // Expand - show full message
                messageSpan.textContent = messageSpan.dataset.full;
                this.innerHTML = '<i class="fas fa-chevron-up me-1"></i>Read Less';
                this.classList.add('expanded');
            }
        });
    });
    
    // ========================================
    // TOGGLE ANNOUNCEMENTS CONTAINER
    // ========================================
    const toggleBtn = document.querySelector('.toggle-announcements-btn');
    const announcementsContainer = document.getElementById('announcementsContainer');
    
    if (toggleBtn && announcementsContainer) {
        // Check localStorage for saved state, default to collapsed (true)
        const savedState = localStorage.getItem('announcementsCollapsed');
        const isCollapsed = savedState === null ? true : savedState === 'true';
        
        // Apply saved state or default
        if (isCollapsed) {
            announcementsContainer.classList.add('collapsed');
            toggleBtn.classList.add('collapsed');
        } else {
            announcementsContainer.classList.remove('collapsed');
            toggleBtn.classList.remove('collapsed');
        }
        
        toggleBtn.addEventListener('click', function() {
            const isCurrentlyCollapsed = announcementsContainer.classList.contains('collapsed');
            
            if (isCurrentlyCollapsed) {
                // Expand
                announcementsContainer.classList.remove('collapsed');
                toggleBtn.classList.remove('collapsed');
                localStorage.setItem('announcementsCollapsed', 'false');
            } else {
                // Collapse
                announcementsContainer.classList.add('collapsed');
                toggleBtn.classList.add('collapsed');
                localStorage.setItem('announcementsCollapsed', 'true');
            }
        });
    }

    // ========================================
    // FETCH DASHBOARD TICKETS
    // ========================================
    function fetchDashboardTickets() {
        const ticketListBody = document.getElementById('ticket-list-body');
        const loadingRow = document.getElementById('tickets-loading');
        const emptyMessage = document.getElementById('no-tickets-message');

        fetch("{{ route('staff.tickets.fetchDashboard') }}", {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (loadingRow) loadingRow.classList.add('d-none');
            
            if (!data || data.length === 0) {
                if (emptyMessage) emptyMessage.classList.remove('d-none');
                return;
            }

            if (ticketListBody) {
                ticketListBody.innerHTML = ''; // Clear loading spinner
                data.forEach(ticket => {
                    const row = document.createElement('tr');
                    
                    // Priority Badge
                    const p = (ticket.priority || '').toLowerCase();
                    let priorityBadgeClass = 'bg-secondary';
                    if (p === 'low')      priorityBadgeClass = 'bg-success';
                    if (p === 'medium')   priorityBadgeClass = 'bg-warning text-dark';
                    if (p === 'high')     priorityBadgeClass = 'bg-danger';
                    if (p === 'urgent')   priorityBadgeClass = 'bg-danger';
                    if (p === 'critical') priorityBadgeClass = 'bg-dark';

                    // Status Badge
                    const s = (ticket.status || '').toLowerCase();
                    let statusBadgeClass = 'bg-secondary';
                    if (s === 'open')        statusBadgeClass = 'bg-success';
                    if (s === 'waiting')     statusBadgeClass = 'bg-info';
                    if (s === 'in_progress') statusBadgeClass = 'bg-warning text-dark';
                    if (s === 'pending')     statusBadgeClass = 'bg-warning text-dark';
                    if (s === 'resolved')    statusBadgeClass = 'bg-primary';
                    if (s === 'closed')      statusBadgeClass = 'bg-secondary';
                    
                    row.innerHTML = `
                        <td class="py-2 ps-3 align-middle">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-ticket-alt text-primary"></i>
                                </div>
                                <div>
                                    <a href="#" class="fw-bold text-dark text-decoration-none btn-detail-ticket" data-id="${ticket.id}">
                                        ${ticket.ticket_id || ticket.id}
                                    </a>
                                    <div class="small text-muted" style="font-size: 0.75rem;">
                                        <i class="fas fa-history me-1"></i>${ticket.updated_at_formatted || ticket.created_at_formatted || 'Just now'}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-2 align-middle">
                            <div class="d-flex flex-column">
                                <div class="text-dark fw-medium text-truncate" style="max-width: 220px;" title="${ticket.description || ''}">
                                    ${ticket.description ? ticket.description : '-'}
                                </div>
                                <div class="small text-muted mt-1">
                                    <i class="fas fa-tag me-1" style="font-size: 0.7rem;"></i>${ticket.category ? ticket.category.name : 'Uncategorized'}
                                </div>
                            </div>
                        </td>
                        <td class="py-2 align-middle">
                            <span class="badge ${priorityBadgeClass} rounded-pill px-3 py-2" style="font-size: 0.7rem; font-weight: 600; letter-spacing: 0.5px;">
                                ${ticket.priority ? ticket.priority.toUpperCase() : '-'}
                            </span>
                        </td>
                        <td class="py-2 align-middle">
                            <span class="badge ${statusBadgeClass} rounded-pill px-3 py-2" style="font-size: 0.7rem; font-weight: 600;">
                                ${ticket.status ? ticket.status.replace('_', ' ').toUpperCase() : '-'}
                            </span>
                        </td>
                        <td class="py-2 align-middle text-end pe-3">
                            <button class="btn btn-sm btn-outline-primary btn-detail-ticket rounded-pill px-3 py-1" data-id="${ticket.id}" style="font-size: 0.75rem;">
                                <i class="fas fa-eye me-1"></i>View
                            </button>
                        </td>
                    `;
                    ticketListBody.appendChild(row);
                });
            }
        })
        .catch(error => {
            console.error('Error fetching tickets:', error);
            if (loadingRow) {
                loadingRow.querySelector('p').textContent = 'Failed to load tickets. Please refresh.';
                loadingRow.querySelector('.spinner-border').classList.add('text-danger');
            }
        });
    }

    // Initial fetch
    fetchDashboardTickets();
});
</script>



</div> <!-- end zoom -->

{{-- Ticket Modal (Create & Detail) --}}
@include('staff.modals.form-ticket')
@include('staff.modals.show-ticket-modal')
@endsection