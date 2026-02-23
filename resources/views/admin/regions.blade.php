@extends('layouts.admin')

@section('title', 'Region Management')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title"><i class="fas fa-globe-asia me-2"></i> Regions</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#regionModal" onclick="openCreateModal()">
            <i class="fas fa-plus me-2"></i> Add Region
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Region Name</th>
                        <th>Locations</th>
                        <th class="text-center" style="width: 130px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($regions as $index => $region)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-bold text-primary">{{ $region->name }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $region->locations_count ?? $region->locations->count() }} location(s)</span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-info text-white me-1" onclick="editRegion({{ $region->id }})" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteRegion({{ $region->id }})" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fas fa-globe fa-2x mb-2"></i><br>
                                No regions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


{{-- Modal --}}
<div class="modal fade" id="regionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="regionForm" method="POST" action="{{ route('admin.regions.store') }}" class="modal-content">
            @csrf
            <div id="methodField"></div>
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Add Region</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Region Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="regionName" class="form-control" placeholder="e.g. Regional 1" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openCreateModal() {
        document.getElementById('regionForm').reset();
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('regionForm').action = "{{ route('admin.regions.store') }}";
        document.getElementById('modalTitle').innerText = 'Add Region';
    }

    function editRegion(id) {
        fetch(`/admin/regions/${id}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('regionName').value = data.name;
                document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
                document.getElementById('regionForm').action = `/admin/regions/${id}`;
                document.getElementById('modalTitle').innerText = 'Edit Region';
                new bootstrap.Modal(document.getElementById('regionModal')).show();
            });
    }

    function deleteRegion(id) {
        Swal.fire({
            title: 'Delete Region?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/regions/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Deleted!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Cannot Delete', data.message, 'error');
                    }
                });
            }
        });
    }
</script>
@endpush
