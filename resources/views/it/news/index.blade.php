@extends('layouts.it')

@section('title', 'News Management - IT Team')

@section('content')
<div class="container py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary mb-0">
            <i class="bi bi-newspaper"></i> News Management
        </h2>
        <a href="{{ route('news.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Add News
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- News List -->
    <div class="row g-4">
        @forelse($news as $item)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow h-100">
                    <div class="card-body d-flex flex-column">
                        <!-- Category Badge -->
                        <div class="mb-2">
                            <span class="badge bg-info text-dark">
                                <i class="bi bi-tag-fill me-1"></i> {{ $item->category->name }}
                            </span>
                        </div>

                        <!-- Message -->
                        <p class="text-muted small flex-grow-1">
                            {{ Str::limit($item->message, 120) }}
                        </p>

                        <!-- Date -->
                        <div class="text-muted small mb-3">
                            <i class="bi bi-calendar-event me-1"></i> 
                            {{ $item->created_at->format('d M Y, H:i') }}
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-auto">
                            <a href="{{ route('news.edit', $item) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <form action="{{ route('news.destroy', $item) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash3"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="col-12">
                <div class="alert alert-light border shadow-sm text-center py-4">
                    <i class="bi bi-info-circle text-secondary fs-3 d-block mb-2"></i>
                    <p class="mb-1">No news available.</p>
                    <a href="{{ route('news.create') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Add a new one
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $news->links() }}
    </div>
</div>
@endsection
