@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <!-- Dashboard Section -->
    <div id="dashboard" class="content-section active">

        <!-- Welcome Section -->
        <div class="welcome-section mb-4">
            <div class="welcome-content">
                <div class="welcome-text">
                    <h2>Selamat Datang, {{ Auth::user()->name }}!</h2>
                    <p>Selamat bekerja! Berikut adalah ringkasan sistem hari ini.</p>
                    <div class="current-time" id="currentTime"></div>
                </div>
                <div class="welcome-icon">
                    <i class="fas fa-hand-wave"></i>
                </div>
            </div>
        </div>

        <!-- 📊 Statistik Section -->
        <div class="stats-grid mb-5">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $totalUsers }}</h3>
                    <p>Total Users</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $totalTickets }}</h3>
                    <p>Total Tickets</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $pendingTickets }}</h3>
                    <p>Pending Tickets</p>
                </div>
            </div>
        </div>
@endsection
