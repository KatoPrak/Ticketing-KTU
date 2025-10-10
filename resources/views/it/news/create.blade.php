@extends('layouts.it')

@section('title', 'Add New News')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-primary mb-0"><i class="bi bi-plus-circle me-2"></i> Add New News</h2>
                <a href="{{ route('it.news.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('it.news.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="message" class="form-label fw-semibold">News Message</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Save News</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection