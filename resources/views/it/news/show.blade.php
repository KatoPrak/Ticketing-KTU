<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $newsItem->title }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    body {
        background-color: #f5f7fa;
    }
    .card-news {
        border-radius: 1rem;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .card-header {
        border-top-left-radius: 1rem;
        border-top-right-radius: 1rem;
        background: linear-gradient(90deg, #0d6efd, #6610f2);
        color: white;
    }
    .news-meta i {
        color: #0d6efd;
    }
    .content {
        line-height: 1.7;
        font-size: 1.05rem;
    }
    .btn-modern:hover {
        color: white !important;
    }
</style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-news">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $newsItem->title }}</h4>
                    <a href="{{ route('news.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <!-- Meta Info -->
                    <div class="d-flex justify-content-between align-items-center news-meta mb-4">
                        <div>
                            <i class="fas fa-user me-1"></i>
                            <strong>{{ $newsItem->creator->name ?? 'Unknown' }}</strong>
                        </div>
                        <div>
                            <i class="fas fa-calendar me-1"></i>
                            {{ $newsItem->created_at->format('d F Y, H:i') }} WIB
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="content mb-4">
                        {!! nl2br(e($newsItem->content)) !!}
                    </div>

                    <!-- Actions -->
                    @if($newsItem->created_by == auth()->id() || auth()->user()->is_admin ?? false)
                        <div class="d-flex gap-2">
                            <a href="{{ route('news.edit', $newsItem->id) }}" class="btn btn-warning btn-modern">
                                <i class="fas fa-edit me-1"></i> Edit Berita
                            </a>
                            <form action="{{ route('news.destroy', $newsItem->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-modern" 
                                        onclick="return confirm('Yakin ingin menghapus berita ini?')">
                                    <i class="fas fa-trash me-1"></i> Hapus Berita
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
