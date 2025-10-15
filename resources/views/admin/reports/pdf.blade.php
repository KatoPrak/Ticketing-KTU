<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Report - {{ ucfirst($reportType) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #3b82f6;
        }
        .header h1 {
            color: #1f2937;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .header p {
            color: #6b7280;
            font-size: 14px;
        }
        .report-info {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .report-info h3 {
            margin-bottom: 10px;
            color: #374151;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-label {
            font-weight: 600;
            color: #374151;
        }
        .info-value {
            color: #6b7280;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .data-table th {
            background: #f8fafc;
            padding: 10px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border: 1px solid #e5e7eb;
            font-size: 12px;
        }
        .data-table td {
            padding: 8px;
            border: 1px solid #f1f5f9;
            font-size: 12px;
        }
        .data-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .table-caption {
            font-size: 11px;
            color: #9ca3af;
            text-align: right;
            margin-top: -5px;
            margin-bottom: 20px;
            font-style: italic;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }
        @media print {
            body { margin: 0; }
            .page-break { page-break-before: always; }
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>Admin Report</h1>
        <p>{{ ucfirst($reportType) }} Report | {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
    </div>

    {{-- Report Info --}}
    <div class="report-info">
        <h3>Report Information</h3>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Report Type:</span>
                <span class="info-value">{{ ucfirst($reportType) }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Date Range:</span>
                <span class="info-value">{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Generated At:</span>
                <span class="info-value">{{ $generatedAt->format('M d, Y H:i') }}</span>
            </div>
        </div>
    </div>

    {{-- === Kondisi untuk Report Type === --}}
    @if ($reportType === 'user')
        {{-- 🧍 USER REPORT --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $totalUsers }}</div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $activeUsers ?? 0 }}</div>
                <div class="stat-label">Active Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $inactiveUsers ?? 0 }}</div>
                <div class="stat-label">Inactive Users</div>
            </div>
        </div>

        {{-- Role Statistics --}}
        @if(!empty($roles))
            <div class="section">
                <h3 class="section-title">Role Statistics</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th>Total Users</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td>{{ ucfirst($role->role) }}</td>
                                <td>{{ $role->total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="table-caption">Data diambil dari sistem Helpdesk per {{ now()->format('d M Y, H:i') }}</div>
            </div>
        @endif

    @else
        {{-- 🎟️ TICKET REPORT --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $totalUsers }}</div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $totalTickets }}</div>
                <div class="stat-label">Total Tickets</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $resolvedTickets }}</div>
                <div class="stat-label">Resolved Tickets</div>
            </div>
        </div>

        {{-- Category Statistics --}}
        @if(!empty($categoryStats))
            <div class="section">
                <h3 class="section-title">Category Statistics</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Total Tickets</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categoryStats as $key => $value)
                            <tr>
                                <td>{{ is_array($value) ? ($value['name'] ?? $key) : $key }}</td>
                                <td>{{ is_array($value) ? ($value['count'] ?? 0) : $value }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="table-caption">Data diambil dari sistem Helpdesk per {{ now()->format('d M Y, H:i') }}</div>
            </div>
        @endif

        {{-- Department Statistics --}}
        @if(!empty($departmentStats))
            <div class="section">
                <h3 class="section-title">Department Statistics</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Total Tickets</th>
                        </tr>
                    </thead>
                        <tbody>
                        @foreach($departmentStats as $key => $value)
                            <tr>
                                <td>{{ $value->name ?? $key }}</td>
                                <td>{{ $value->count ?? 0 }}</td>
                            </tr>
                        @endforeach
                        </tbody>

                </table>
                <div class="table-caption">Data diambil dari sistem Helpdesk per {{ now()->format('d M Y, H:i') }}</div>
            </div>
        @endif
    @endif

    {{-- Footer --}}
    <div class="footer">
        Generated by Helpdesk System &mdash; {{ now()->format('M d, Y H:i') }}
    </div>
</body>
</html>
