@extends('layouts.it')

@section('title', 'Edit News - IT Team')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <!-- Card -->
            <div class="card border-0 shadow-lg rounded-3">
                <!-- Header -->
                <div class="card-header bg-warning text-dark d-flex align-items-center">
                    <i class="bi bi-pencil-square fs-4 me-2"></i>
                    <h4 class="mb-0 fw-bold">Edit News</h4>
                </div>

                <!-- Body -->
                <div class="card-body p-4">
                    <form action="{{ route('news.update', $news->id) }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')

                        <!-- Category -->
                        <div class="mb-4">
                            <label for="category_id" class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" 
                                class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                        {{ (old('category_id', $news->category_id) == $category->id) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Message -->
                        <div class="mb-4">
                            <label for="message" class="form-label fw-semibold">Message <span class="text-danger">*</span></label>
                            <textarea name="message" id="message" rows="6" 
                                class="form-control @error('message') is-invalid @enderror" 
                                placeholder="Write your news message here..." required>{{ old('message', $news->message) }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('news.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-arrow-left-circle me-1"></i> Back
                            </a>
                            <button type="submit" class="btn btn-warning px-4">
                                <i class="bi bi-save me-1"></i> Update News
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- End Card -->
        </div>
    </div>
</div>
@endsection
