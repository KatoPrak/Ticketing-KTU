<div class="row g-4">

    <!-- Left Column -->
    <div class="col-md-6">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h6 class="text-muted small mb-2"><i class="fas fa-align-left me-2"></i>Deskripsi</h6>
                <p class="fw-semibold">{{ $ticket->description }}</p>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body d-flex align-items-center">
                <i class="fas fa-layer-group me-2 text-info"></i>
                <span class="badge bg-info text-dark">{{ $ticket->category->name ?? '-' }}</span>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-md-6">
        <div class="card shadow-sm mb-3">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted small mb-1"><i class="fas fa-flag me-2"></i>Status</h6>
                    <span class="badge 
                        {{ $ticket->status=='open' ? 'bg-warning text-dark' : 
                           ($ticket->status=='progress' ? 'bg-info text-dark' : 
                           ($ticket->status=='done' ? 'bg-success text-white' : 'bg-secondary text-white')) }}">
                        {{ ucfirst($ticket->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted small mb-1"><i class="fas fa-exclamation-circle me-2"></i>Prioritas</h6>
                    <span class="badge 
                        {{ $ticket->priority=='high' ? 'bg-danger' : 
                           ($ticket->priority=='medium' ? 'bg-primary' : 'bg-secondary') }}">
                        {{ ucfirst($ticket->priority) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Lampiran -->
@if($ticket->attachments && $ticket->attachments->count() > 0)
<hr>
<h5 class="mt-4 mb-3"><i class="fas fa-paperclip me-2"></i>Lampiran</h5>
<div class="row g-3">
    @foreach($ticket->attachments as $file)
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body text-center p-2">
                <i class="fas fa-file-alt fa-2x mb-2 text-secondary"></i>
                <p class="small text-truncate">{{ $file->filename }}</p>
                <a href="{{ asset('storage/' . $file->path) }}" target="_blank"
                    class="btn btn-sm btn-outline-primary">Buka</a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
