@extends('layouts.it')

@section('title', 'News Management')

@section('content')
<div class="container py-5">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0"><i class="bi bi-newspaper me-2"></i> News Management</h2>
        <a href="{{ route('it.news.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center gap-1">
            <i class="bi bi-plus-circle"></i> Add News
        </a>
    </div>

    {{-- Pesan Sukses --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Tabel News --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase small text-muted">
                    <tr>
                        <th class="ps-4" style="width:5%">#</th>
                        <th>Message</th>
                        <th style="width:20%">Created At</th>
                        <th class="text-center" style="width:15%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($news as $item)
                    <tr>
                        <th class="ps-4">{{ $loop->iteration }}</th>
                        <td class="text-truncate" style="max-width:400px;" title="{{ $item->message }}">{{ Str::limit($item->message, 80) }}</td>
                        <td>
                            <span class="badge bg-info text-dark">{{ $item->created_at->format('d M Y, H:i') }}</span>
                        </td>
                        <td class="text-center">
                            {{-- Delete Button --}}
<button type="button" 
        class="btn btn-sm btn-danger" 
        data-bs-toggle="modal" 
        data-bs-target="#deleteModal"
        data-bs-url="{{ route('it.news.destroy', $item->id) }}">
    Delete
</button>

                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="bi bi-info-circle fs-2 d-block mb-2"></i>
                            No news available.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this news item?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Set action form delete saat modal muncul
    const deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const url = button.getAttribute('data-bs-url');
        document.getElementById('deleteForm').action = url;
    });
</script>
@endpush
