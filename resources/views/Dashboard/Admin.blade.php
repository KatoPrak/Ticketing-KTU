<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - IT Ticketing System</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            padding: 20px 0;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .logo {
            text-align: center;
            padding: 0 20px 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 30px;
        }

        .logo h2 {
            color: white;
            font-size: 24px;
            font-weight: 600;
        }

        .logo span {
            color: #ffd700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .nav-menu {
            list-style: none;
            padding: 0 10px;
        }

        .nav-item {
            margin-bottom: 5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateX(5px);
        }

        .nav-link i {
            margin-right: 15px;
            font-size: 18px;
            width: 20px;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            padding: 20px 30px;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header h1 {
            color: white;
            font-size: 28px;
            font-weight: 600;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notification-btn,
        .profile-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            padding: 12px;
            border-radius: 10px;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .notification-btn:hover,
        .profile-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 25px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4);
            background-size: 400% 400%;
            animation: gradient 3s ease infinite;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: white;
            margin-bottom: 5px;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-change {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 20px;
            font-weight: 600;
        }

        .stat-change.positive {
            background: rgba(76, 175, 80, 0.3);
            color: #4caf50;
        }

        .stat-change.negative {
            background: rgba(244, 67, 54, 0.3);
            color: #f44336;
        }

        /* Content Sections */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .content-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 25px;
        }

        .card-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .card-title {
            color: white;
            font-size: 20px;
            font-weight: 600;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        /* Table */
        .table-container {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .table th {
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 12px;
        }

        .table td {
            color: rgba(255, 255, 255, 0.9);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .status-open {
            background: rgba(255, 193, 7, 0.3);
            color: #ffc107;
        }

        .status-progress {
            background: rgba(33, 150, 243, 0.3);
            color: #2196f3;
        }

        .status-closed {
            background: rgba(76, 175, 80, 0.3);
            color: #4caf50;
        }

        .priority-high {
            background: rgba(244, 67, 54, 0.3);
            color: #f44336;
        }

        .priority-medium {
            background: rgba(255, 152, 0, 0.3);
            color: #ff9800;
        }

        .priority-low {
            background: rgba(76, 175, 80, 0.3);
            color: #4caf50;
        }

        /* Chart Container */
        .chart-container {
            position: relative;
            height: 300px;
        }

        /* Activity Feed */
        .activity-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 16px;
            color: white;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            color: white;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .activity-time {
            color: rgba(255, 255, 255, 0.6);
            font-size: 12px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }

            .content-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

    </style>
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <h2>IT Desk</h2>
                <span>Admin Panel</span>
            </div>
            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-ticket-alt"></i>
                            <span>Tickets</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-users"></i>
                            <span>Users</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-user-tie"></i>
                            <span>Agents</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-chart-bar"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-tags"></i>
                            <span>Categories</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <h1>Dashboard Overview</h1>
                <div class="header-actions">
                    <button class="notification-btn">
                        <i class="fas fa-bell"></i>
                    </button>
                    <button class="profile-btn">
                        <i class="fas fa-user-circle"></i>
                    </button>
                </div>
            </header>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon" style="background: linear-gradient(45deg, #ff6b6b, #ff8e8e);">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <span class="stat-change positive">+12%</span>
                    </div>
                    <div class="stat-number">1,247</div>
                    <div class="stat-label">Total Tickets</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon" style="background: linear-gradient(45deg, #4ecdc4, #6ee7dc);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <span class="stat-change negative">-5%</span>
                    </div>
                    <div class="stat-number">89</div>
                    <div class="stat-label">Open Tickets</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon" style="background: linear-gradient(45deg, #45b7d1, #6ec5e0);">
                            <i class="fas fa-users"></i>
                        </div>
                        <span class="stat-change positive">+8%</span>
                    </div>
                    <div class="stat-number">456</div>
                    <div class="stat-label">Active Users</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon" style="background: linear-gradient(45deg, #96ceb4, #a8d5c2);">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <span class="stat-change positive">+15%</span>
                    </div>
                    <div class="stat-number">94%</div>
                    <div class="stat-label">Resolution Rate</div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="content-grid">
                <!-- Recent Tickets -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Tickets</h3>
                        <button class="btn btn-primary">View All</button>
                    </div>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Subject</th>
                                    <th>User</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody id="ticketsTableBody">
                                <!-- Table rows will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Activity Feed -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Activity</h3>
                    </div>
                    <div class="activity-feed" id="activityFeed">
                        <!-- Activity items will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Tickets Analytics</h3>
                </div>
                <div class="chart-container">
                    <canvas id="ticketsChart"></canvas>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Sample data
        const ticketsData = [{
                id: '#T001',
                subject: 'Email server down',
                user: 'John Doe',
                priority: 'High',
                status: 'Open',
                created: '2 hours ago'
            },
            {
                id: '#T002',
                subject: 'Password reset request',
                user: 'Jane Smith',
                priority: 'Medium',
                status: 'Progress',
                created: '4 hours ago'
            },
            {
                id: '#T003',
                subject: 'VPN connection issues',
                user: 'Mike Johnson',
                priority: 'High',
                status: 'Open',
                created: '6 hours ago'
            },
            {
                id: '#T004',
                subject: 'Software installation',
                user: 'Sarah Wilson',
                priority: 'Low',
                status: 'Closed',
                created: '1 day ago'
            },
            {
                id: '#T005',
                subject: 'Network connectivity',
                user: 'Tom Brown',
                priority: 'Medium',
                status: 'Progress',
                created: '1 day ago'
            }
        ];

        const activityData = [{
                icon: 'fas fa-plus',
                bg: '#4ecdc4',
                title: 'New ticket created',
                time: '5 minutes ago'
            },
            {
                icon: 'fas fa-user',
                bg: '#45b7d1',
                title: 'User registered',
                time: '15 minutes ago'
            },
            {
                icon: 'fas fa-check',
                bg: '#96ceb4',
                title: 'Ticket resolved',
                time: '30 minutes ago'
            },
            {
                icon: 'fas fa-comment',
                bg: '#ff6b6b',
                title: 'New comment added',
                time: '1 hour ago'
            },
            {
                icon: 'fas fa-cog',
                bg: '#ffd93d',
                title: 'System maintenance',
                time: '2 hours ago'
            }
        ];

        // Populate tickets table
        function populateTicketsTable() {
            const tbody = document.getElementById('ticketsTableBody');
            tbody.innerHTML = '';

            ticketsData.forEach(ticket => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${ticket.id}</td>
                    <td>${ticket.subject}</td>
                    <td>${ticket.user}</td>
                    <td><span class="status-badge priority-${ticket.priority.toLowerCase()}">${ticket.priority}</span></td>
                    <td><span class="status-badge status-${ticket.status.toLowerCase()}">${ticket.status}</span></td>
                    <td>${ticket.created}</td>
                `;
                tbody.appendChild(row);
            });
        }

        // Populate activity feed
        function populateActivityFeed() {
            const feed = document.getElementById('activityFeed');
            feed.innerHTML = '';

            activityData.forEach(activity => {
                const item = document.createElement('div');
                item.className = 'activity-item';
                item.innerHTML = `
                    <div class="activity-icon" style="background: ${activity.bg};">
                        <i class="${activity.icon}"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">${activity.title}</div>
                        <div class="activity-time">${activity.time}</div>
                    </div>
                `;
                feed.appendChild(item);
            });
        }

        // Initialize chart
        function initChart() {
            const ctx = document.getElementById('ticketsChart').getContext('2d');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'New Tickets',
                        data: [12, 19, 8, 15, 23, 17, 14],
                        borderColor: 'rgba(102, 126, 234, 1)',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Resolved Tickets',
                        data: [8, 15, 12, 18, 20, 15, 16],
                        borderColor: 'rgba(78, 205, 196, 1)',
                        backgroundColor: 'rgba(78, 205, 196, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: 'white'
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: 'rgba(255, 255, 255, 0.8)'
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        },
                        y: {
                            ticks: {
                                color: 'rgba(255, 255, 255, 0.8)'
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        }
                    }
                }
            });
        }

        // Navigation functionality
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                link.classList.add('active');
            });
        });

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', () => {
            populateTicketsTable();
            populateActivityFeed();
            initChart();
        });

        // Auto-refresh data every 30 seconds
        setInterval(() => {
            // In a real application, this would fetch fresh data from the server
            console.log('Refreshing dashboard data...');
        }, 30000);

    </script>
    @if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('
            success ') }}',
            showConfirmButton: false,
            timer: 2000
        });

    </script>
    @endif
</body>

</html>
