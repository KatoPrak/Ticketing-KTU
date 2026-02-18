@extends('layouts.admin')

@section('title', 'User Management')


@section('content')
@includeWhen(session('success') || session('error') || $errors->any(), 'admin.partials.alerts')
<link rel="stylesheet" href="{{ asset('build/assets/admin-BJfupJ7l.css') }}">
<script src="{{ asset('build/assets/admin-CBTWxBS5.js') }}"></script>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">User Management</h3>

        <div class="d-flex gap-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Search by Staff ID..." style="width: 250px;">
            <button class="btn btn-primary open-user-modal">
                <i class="fas fa-plus me-1"></i> Add User
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table id="usersTable" class="table table-striped align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Staff ID</th>
                    <th>Location / Region</th>
                    <th>Role</th>
                    <th>Department</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users ?? [] as $user)
                    @if($user->role !== 'admin')
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td class="staff-id">{{ $user->id_staff }}</td>
                            <td>
                                @if(in_array($user->role, ['tim it', 'it']))
                                    <span class="badge bg-primary"><i class="fas fa-globe me-1"></i> {{ $user->region->name ?? 'Unassigned' }}</span>
                                @else
                                    <span class="badge bg-info text-dark">{{ $user->location->name ?? '-' }}</span>
                                @endif
                            </td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td>
                                <span class="badge bg-success">
                                    {{ strtoupper(optional($user->department)->name ?? '-') }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary edit-user" data-id="{{ $user->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-user" data-id="{{ $user->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr id="noUsersRow">
                        <td colspan="6" class="text-center text-muted">No users found</td>
                    </tr>
                @endforelse
                <tr id="noResultsRow" style="display: none;">
                    <td colspan="6" class="text-center text-muted">No users match your search</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- ✅ Include User Modal --}}
@include('admin.partials.user-modal')

@endsection
