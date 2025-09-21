@extends('layouts.admin')

@section('title', 'Ticket List')

@section('content')
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
                    {{-- Data will be populated from the controller --}}
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
                </tbody>
            </table>
        </div>
    </div>
@endsection