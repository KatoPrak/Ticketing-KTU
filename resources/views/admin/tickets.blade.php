@extends('layouts.admin')

@section('title', 'Ticket List')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ticket List</h3>
            {{-- Tombol export PDF kita modifikasi agar membawa parameter filter --}}
            <a href="{{ route('admin.tickets.export.pdf', request()->query()) }}" class="btn btn-success float-right ml-2">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>

        {{-- ================= FORM FILTER MULAI DARI SINI ================= --}}
        <div class="card-body">
            <form action="{{ route('admin.tickets.index') }}" method="GET" class="form-inline">
                <div class="form-group mb-2">
                    <label for="year" class="mr-2">Tahun:</label>
                    <select name="year" id="year" class="form-control">
                        <option value="">All Years</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mx-sm-3 mb-2">
                    <label for="month" class="mr-2">Bulan:</label>
                    <select name="month" id="month" class="form-control">
                        <option value="">All Months</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>

                <button type="submit" class="btn btn-primary mb-2"><i class="fas fa-filter"></i> Filter</button>
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary mb-2 ml-2"><i class="fas fa-sync"></i> Reset</a>
            </form>
        </div>
        {{-- ================= FORM FILTER SELESAI ================= --}}

        <div class="table-responsive">
            <table id="ticketsTable" class="table table-bordered">
                {{-- ... isi tabel Anda tidak berubah ... --}}
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
                    @forelse($tickets as $ticket) {{-- Ganti @foreach jadi @forelse untuk handle data kosong --}}
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
                    @empty
                        {{-- Pesan jika tidak ada data tiket yang ditemukan --}}
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data tiket yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection