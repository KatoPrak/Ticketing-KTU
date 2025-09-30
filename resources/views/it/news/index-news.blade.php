@extends('layouts.it')

@section('title', 'News Dashboard')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">
            <i class="fas fa-newspaper me-2"></i> News Dashboard
        </h2>
        <a href="{{ route('news.create') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-plus me-1"></i> Tambah Berita
        </a>
    </div>

    <!-- Search -->
    <div class="card shadow-sm mb-4 search-card p-3">
        <form method="GET" action="{{ route('news.index') }}">
            <div class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control form-control-lg"
                           placeholder="Cari berita..." value="{{ $search ?? '' }}">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-primary btn-lg w-100 btn-outline-modern">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- News List -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @forelse($news as $item)
            <div class="col">
                <div class="card card-news h-100 position-relative" style="border-radius: 1rem; transition: transform 0.2s, box-shadow 0.2s;">
                    <span class="badge badge-category" style="position: absolute; top: 15px; right: 15px; font-size: 0.75rem; padding: 0.35em 0.7em; background-color: #0d6efd;">News</span>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $item->title }}</h5>
                        <p class="card-text text-muted flex-grow-1">{{ $item->excerpt }}</p>
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i> {{ $item->creator->name ?? 'Unknown' }}<br>
                                <i class="fas fa-calendar me-1"></i> {{ $item->created_at->format('d M Y') }}
                            </small>
                        </div>
                        <div class="mt-3 d-flex justify-content-between">
                            <a href="{{ route('news.show', $item->id) }}" class="btn btn-primary btn-sm w-50 me-1">
                                <i class="fas fa-eye me-1"></i> Baca
                            </a>
                            @if($item->created_by == auth()->id() || auth()->user()->is_admin ?? false)
                                <a href="{{ route('news.edit', $item->id) }}" class="btn btn-warning btn-sm w-50 me-1">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                <form action="{{ route('news.destroy', $item->id) }}" method="POST" class="d-inline w-50">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm w-100" onclick="return confirm('Yakin ingin menghapus?')">
                                        <i class="fas fa-trash me-1"></i> Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-newspaper fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Belum ada berita</h4>
                <p class="text-muted">Mulai dengan menambahkan berita pertama!</p>
                <a href="{{ route('news.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-1"></i> Tambah Berita
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $news->links() }}
    </div>
</div>

<!-- Custom Styles -->
@push('styles')
<style>
    .card-news:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    .btn-outline-modern:hover {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }
</style>
@endpush
@endsection
