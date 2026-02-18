@extends('layouts.admin')

@section('title', 'Location Management')


@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title"><i class="fas fa-map-marker-alt me-2"></i> Locations</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#locationModal" onclick="openCreateModal()">
            <i class="fas fa-plus me-2"></i> Add Location
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Name</th>
                        <th>Region</th>
                        <th class="text-center" style="width: 150px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($locations as $index => $location)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-bold text-primary">{{ $location->name }}</td>
                            <td>
                                @if($location->region)
                                    <span class="badge bg-secondary">{{ $location->region->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-info text-white me-1" onclick="editLocation({{ $location->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteLocation({{ $location->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">
                                <i class="fas fa-map-marker-slash fa-2x mb-2"></i><br>
                                No locations found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="locationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="locationForm" method="POST" action="{{ route('admin.locations.store') }}" class="modal-content">
            @csrf
            <div id="methodField"></div>
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Add Location</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Region <span class="text-danger">*</span></label>
                    <select name="region_id" id="region_id" class="form-select" required>
                        <option value="">Select Region</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}">{{ $region->name }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openCreateModal() {
        document.getElementById('locationForm').reset();
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('locationForm').action = "{{ route('admin.locations.store') }}";
        document.getElementById('modalTitle').innerText = 'Add Location';
    }

    function editLocation(id) {
        fetch(`/admin/locations/${id}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('name').value = data.name;
                document.getElementById('region_id').value = data.region_id;
                document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
                document.getElementById('locationForm').action = `/admin/locations/${id}`;
                document.getElementById('modalTitle').innerText = 'Edit Location';
                new bootstrap.Modal(document.getElementById('locationModal')).show();
            });
    }

    function deleteLocation(id) {
        if(!confirm('Are you sure you want to delete this location?')) return;

        fetch(`/admin/locations/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }
</script>
@endpush
