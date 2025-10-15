@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
{{-- Card 1: Generate Report --}}
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Generate Reports</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.export.pdf') }}">
    <div class="row align-items-end">
        <div class="col-md-4 mb-3">
            <label class="form-label">Report Type</label>
            <select class="form-control" name="reportType">
                <option value="tickets" {{ request('reportType') == 'tickets' ? 'selected' : '' }}>Tickets Report</option>
                <option value="users" {{ request('reportType') == 'users' ? 'selected' : '' }}>Users Report</option>
                <option value="summary" {{ request('reportType') == 'summary' ? 'selected' : '' }}>Summary Report</option>
            </select>
        </div>

        <div class="col-md-4 mb-3">
            <label class="form-label">Date Range</label>
            <select class="form-control" name="dateRange">
                <option value="week" {{ request('dateRange') == 'week' ? 'selected' : '' }}>Last Week</option>
                <option value="month" {{ request('dateRange') == 'month' ? 'selected' : '' }}>Last Month</option>
                <option value="year" {{ request('dateRange') == 'year' ? 'selected' : '' }}>Last Year</option>
            </select>
        </div>

        <div class="col-md-4 mb-3">
            <button type="submit" class="btn btn-danger w-100">
                <i class="fas fa-file-pdf"></i> Generate PDF Report
            </button>
        </div>
    </div>
</form>
    </div>
</div>

@endsection
