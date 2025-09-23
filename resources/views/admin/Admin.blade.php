<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Dashboard </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('assets/image/logo-ktu.jpg') }}">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3>Admin Panel</h3>
                <button class="toggle-btn toggle-sidebar">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="sidebar-menu">
                <a href="#" class="menu-item active" data-section="dashboard">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="menu-item" data-section="users">
                    <i class="fas fa-users"></i>
                    <span>Manage Users</span>
                </a>
                <a href="#" class="menu-item" data-section="tickets">
                    <i class="fas fa-ticket-alt"></i>
                    <span>Tickets</span>
                </a>
                <a href="#" class="menu-item" data-section="reports">
                    <i class="fas fa-file-pdf"></i>
                    <span>Reports</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Navbar -->
            <div class="navbar">
                <div class="navbar-title">Dashboard</div>
                <div class="navbar-user">
                    <!-- Bagian yang diklik -->
                    <div class="user-info">
                        <span>{{ Auth::user()->name }}</span>
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>

                    <!-- Dropdown -->
                    <div id="userDropdown" class="user-dropdown">
                        <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); logout();">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="content">
                <!-- Dashboard Section -->
                <div id="dashboard" class="content-section active">
                    <!-- Welcome Section -->
                    <div class="welcome-section">
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

                    <!-- Stats Grid -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon blue">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-info">
                                <h3>24</h3>
                                <p>Total Users</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon green">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <div class="stat-info">
                                <h3>157</h3>
                                <p>Total Tickets</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon orange">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-info">
                                <h3>12</h3>
                                <p>Pending Tickets</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users Section -->
                <div id="users" class="content-section">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">User Management</h3>
                            <button class="btn btn-primary open-user-modal">
                                <i class="fas fa-plus"></i> Add User
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="usersTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>ID Staff</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Department</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Jane Smith</td>
                                        <td>ID sTAFF</td>
                                        <td>jane@example.com</td>
                                        <td>IT Team</td>
                                        <td><span class="badge badge-success">ENGGINEER</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary edit-user" data-id="2">Edit</button>
                                            <button class="btn btn-sm btn-danger delete-user"
                                                data-id="2">Delete</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tickets Section -->
                <div id="tickets" class="content-section">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Ticket List</h3>
                            <button class="btn btn-success export-pdf">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="ticketsTable">
                                <thead>
                                    <tr>
                                        <th>Ticket ID</th>
                                        <th>Subject</th>
                                        <th>User</th>
                                        <th>Category</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>#TK001</td>
                                        <td>Network Issue</td>
                                        <td>John Doe</td>
                                        <td>IT Support</td>
                                        <td><span class="badge badge-danger">High</span></td>
                                        <td><span class="badge badge-warning">In Progress</span></td>
                                        <td>2024-01-15</td>
                                    </tr>
                                    <tr>
                                        <td>#TK002</td>
                                        <td>Software Installation</td>
                                        <td>Jane Smith</td>
                                        <td>IT Support</td>
                                        <td><span class="badge badge-warning">Medium</span></td>
                                        <td><span class="badge badge-success">Resolved</span></td>
                                        <td>2024-01-14</td>
                                    </tr>
                                    <tr>
                                        <td>#TK003</td>
                                        <td>Email Access Problem</td>
                                        <td>Mike Johnson</td>
                                        <td>IT Support</td>
                                        <td><span class="badge badge-success">Low</span></td>
                                        <td><span class="badge badge-warning">Pending</span></td>
                                        <td>2024-01-13</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Reports Section -->
                <div id="reports" class="content-section">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Generate Reports</h3>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Report Type</label>
                                <select class="form-control" id="reportType">
                                    <option value="tickets">Tickets Report</option>
                                    <option value="users">Users Report</option>
                                    <option value="summary">Summary Report</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date Range</label>
                                <select class="form-control" id="dateRange">
                                    <option value="week">Last Week</option>
                                    <option value="month">Last Month</option>
                                    <option value="year">Last Year</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-primary generate-report">
                            <i class="fas fa-file-pdf"></i> Generate PDF Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Modal -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>TAMBAHKAN PENGGUNA</h3>
                <span class="close close-user-modal">&times;</span>
            </div>
            <div class="modal-body">
                <form id="userForm" action="{{ route('admin.users.store') }}" method="POST">
    @csrf
    <div class="form-group">
        <label class="form-label">Name</label>
        <input type="text" class="form-control" name="name" required>
    </div>
    <div class="form-group">
        <label class="form-label">ID Staff</label>
        <input type="text" class="form-control" name="staff_id" required>
    </div>
    <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" name="email" required>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label class="form-label">Role</label>
            <select class="form-control" name="role">
                <option value="staff">Staff</option>
                <option value="it">IT Team</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Department</label>
            <select class="form-control" name="department">
                <option value="hse">HSE</option>
                <option value="hr">HR</option>
                <option value="finance">Finance</option>
                <option value="production">Production</option>
                <option value="project">Project</option>
                <option value="security">Security</option>
                <option value="commercial">Commercial</option>
                <option value="purchasing">Purchasing</option>
                <option value="account-payable">Account Payable</option>
                <option value="engineering">Engineering</option>
                <option value="qc">Quality Control</option>
                <option value="store">Store</option>
                <option value="accounting">Accounting</option>
                <option value="inventory">Inventory</option>
                <option value="admin-project">Admin Project</option>
                <option value="iso">ISO</option>
                <option value="ppic">PPIC</option>
                <option value="planner">Planner</option>
                <option value="facility">Facility</option>
                <option value="outfitting">Outfitting</option>
            </select>
        </div>
    </div>

    <!-- Password default (hidden input) -->
    <input type="hidden" name="password" value="STAFFKTU123">

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary close-user-modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save User</button>
    </div>
</form>

        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <script>
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                document.getElementById('logout-form').submit();
            }
        }

        // Function to update current time
        function updateTime() {
            const now = new Date();
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            const timeString = now.toLocaleDateString('id-ID', options);
            document.getElementById('currentTime').textContent = timeString;
        }

        // Update time immediately and then every minute
        updateTime();
        setInterval(updateTime, 60000);

        document.querySelector('.save-user').addEventListener('click', function (e) {
            e.preventDefault();

            const newUser = {
                name: document.getElementById('userName').value,
                staff_id: document.getElementById('userStaffId').value,
                email: document.getElementById('userEmail').value,
                role: document.getElementById('userRole').value,
                department: document.getElementById('userDept').value,
                password: 'STAFFKTU123' // default password
            };

            console.log("User disimpan:", newUser);

            // TODO: Kirim ke backend dengan fetch/axios
            // fetch('/users', { method: 'POST', body: JSON.stringify(newUser) })
        });

    </script>
</body>

</html>
