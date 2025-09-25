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
            <table id="ticketsTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Ticket ID</th>
                        <th>Description</th>
                        <th>User</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                        <tr>
                            <td>#TK{{ str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $ticket->description }}</td>
                            <td>{{ $ticket->user->name ?? 'N/A' }}</td>
                            <td>{{ $ticket->category->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge
                                    @if($ticket->priority === 'high') badge-danger
                                    @elseif($ticket->priority === 'medium') badge-warning
                                    @else badge-success @endif">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge
                                    @if($ticket->status === 'in_progress') badge-warning
                                    @elseif($ticket->status === 'resolved') badge-success
                                    @elseif($ticket->status === 'pending') badge-warning
                                    @elseif($ticket->status === 'waiting') badge-success
                                    @elseif($ticket->status === 'closed') badge-secondary
                                    @else badge-primary @endif">
                                    {{ str_replace('_', ' ', ucfirst($ticket->status)) }}
                                </span>
                            </td>
                            <td>{{ $ticket->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
