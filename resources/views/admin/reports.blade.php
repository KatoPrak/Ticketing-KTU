@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
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
@endsection