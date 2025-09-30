@extends('layouts.it')

@section('title', 'Dashboard - Tim IT')

@section('content')
    <!-- Welcome Card -->
    <div class="welcome-card mb-4">
        <div class="row align-items-center">
            <div class="col-md-8 col-12">
                <h2><i class="fas fa-wave-square me-2"></i>Selamat Datang!</h2>
                <p class="mb-0">{{ Auth::user()->name }} (ID-{{ Auth::user()->id_staff }})</p>
            </div>
            <div class="col-md-4 col-12 text-center text-md-end mt-3 mt-md-0">
                <div class="fs-1">
                    <i class="fas fa-user-tie"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-6">
            <div class="dashboard-card stat-card p-3">
                <div class="stat-number fs-4 fw-bold">{{ $activeTickets ?? 0 }}</div>
                <div class="stat-label small text-muted">Active Tickets</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6">
            <div class="dashboard-card stat-card p-3">
                <div class="stat-number fs-4 fw-bold">{{ $pendingTickets ?? 0 }}</div>
                <div class="stat-label small text-muted">Pending</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6">
            <div class="dashboard-card stat-card p-3">
                <div class="stat-number fs-4 fw-bold">{{ $completedTickets ?? 0 }}</div>
                <div class="stat-label small text-muted">Completed</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-6">
            <div class="dashboard-card stat-card p-3">
                <div class="stat-number fs-4 fw-bold">{{ $urgentTickets ?? 0 }}</div>
                <div class="stat-label small text-muted">Urgent</div>
            </div>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="row">
        <div class="col-lg-8 col-12">
            <div class="dashboard-card p-3">
                <h5 class="mb-3"><i class="fas fa-chart-line me-2"></i>Recent Activity</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Ticket ID</th>
                                <th>Subject</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Date</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $statusClasses = [
                                    'waiting'     => 'bg-secondary',
                                    'in_progress' => 'bg-primary',
                                    'pending'     => 'bg-warning text-dark',
                                    'resolved'    => 'bg-success',
                                    'closed'      => 'bg-dark text-white',
                                ];
                                $priorityClasses = [
                                    'low'    => 'bg-secondary',
                                    'medium' => 'bg-info text-dark',
                                    'high'   => 'bg-danger',
                                    'urgent' => 'bg-danger text-white',
                                ];
                            @endphp

                            @forelse($recentTickets as $ticket)
                                <tr>
                                    <td><strong>{{ $ticket->ticket_id ?? ('#'.$ticket->id) }}</strong></td>
                                    <td>{{ $ticket->category->name ?? 'Umum' }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($ticket->description, 40) }}</td>
                                    <td>
                                        <span class="badge {{ $statusClasses[$ticket->status] ?? 'bg-secondary' }}">
                                            {{ ucfirst(str_replace('_',' ', $ticket->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $priorityClasses[$ticket->priority] ?? 'bg-secondary' }}">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                    </td>
                                    <td>{{ optional($ticket->created_at)->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-detail-ticket"
                                            data-id="{{ $ticket->id }}" data-bs-toggle="modal" data-bs-target="#ticketDetailModal">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Tidak ada aktivitas tiket terbaru
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <!-- Quick Actions -->
<div class="col-lg-4 col-12">
    <div class="dashboard-card p-3">
        <h5 class="mb-3"><i class="fas fa-tasks me-2"></i>Quick Actions</h5>
        <div class="d-grid gap-2">
            <a href="{{ route('news.create') }}" class="btn btn-primary">
                <i class="fas fa-newspaper me-2"></i> Create News
            </a>
        </div>
    </div>
</div>


    <!-- Modal Detail Tiket -->
    <div class="modal fade" id="ticketDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-ticket-alt me-2"></i>Detail Tiket</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="ticketDetailContent">
                    <p class="text-center text-muted">Memuat detail tiket...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".btn-detail-ticket").forEach(btn => {
        btn.addEventListener("click", () => {
            const modalContent = document.getElementById("ticketDetailContent");
            modalContent.innerHTML = `<p class="text-center text-muted">Memuat detail tiket...</p>`;

            fetch(`/it/tickets/${btn.dataset.id}`)
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.text();
                })
                .then(html => modalContent.innerHTML = html)
                .catch(() => {
                    modalContent.innerHTML = `<p class="text-danger text-center">Gagal memuat detail tiket.</p>`;
                });
        });
    });
});
</script>
@endpush
