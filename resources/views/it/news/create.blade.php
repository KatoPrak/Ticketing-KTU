@extends('layouts.it')

@section('title', 'Tambah News - Tim IT')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Tambah News Baru</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('news.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category *</label>
                            <select name="category_id" id="category_id" 
                                    class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Category --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message *</label>
                            <textarea name="message" id="message" rows="6" 
                                      class="form-control @error('message') is-invalid @enderror" 
                                      placeholder="Tulis pesan news disini..." required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('news.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan News
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
