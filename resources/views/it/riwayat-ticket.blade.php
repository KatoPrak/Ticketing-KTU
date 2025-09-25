@extends('layouts.it')

@section('title', 'Riwayat Tiket IT')
@vite(['resources/css/list-tiket.css','resources/js/list-tiket.js'])

@section('content')
<div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title">
            <i class="fas fa-history me-2 text-primary"></i> Riwayat Tiket IT
        </h2>
    </div>

    <!-- Table Tickets -->
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Deskripsi</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Prioritas</th>
                        <th>Dibuat Oleh</th>
                        <th>Terakhir Update</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($tickets as $ticket)
                    <tr>
                        <td>#{{ $ticket->id }}</td>
                        <td>{{ $ticket->description }}</td>
                        <td>{{ $ticket->category->name ?? '-' }}</td>
                        <td><span class="badge bg-secondary">{{ ucfirst($ticket->status) }}</span></td>
                        <td>{{ ucfirst($ticket->priority) }}</td>
                        <td>{{ $ticket->user->name ?? 'Unknown' }}</td>
                        <td>{{ $ticket->updated_at->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Belum ada riwayat tiket</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $tickets->links() }}
        </div>
    </div>
</div>
@endsection
