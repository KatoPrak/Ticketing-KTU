<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: #0d6efd;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
        }
        .info-row {
            margin: 15px 0;
            padding: 12px;
            background: #f8f9fa;
            border-left: 4px solid #0d6efd;
            border-radius: 4px;
        }
        .info-row strong {
            color: #0d6efd;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-low { background: #28a745; color: white; }
        .badge-medium { background: #ffc107; color: #000; }
        .badge-high { background: #dc3545; color: white; }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #0d6efd;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            padding: 15px;
            text-align: center;
            color: #666;
            font-size: 12px;
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            {{-- LOGO DITAMBAHKAN DI SINI --}}
            <img src="{{ asset('assets/image/logo-ktu.jpg') }}" alt="Logo Perusahaan" style="max-width: 150px; height: auto; margin-bottom: 10px;">
            <h2 style="margin: 0;">🎫 Tiket Baru Masuk</h2>
        </div>
        
        <div class="content">
            <p>Halo Tim IT,</p>
            <p>Tiket baru telah dibuat dan memerlukan perhatian Anda.</p>
            
            <div class="info-row">
                <strong>ID Tiket:</strong> {{ $ticket->ticket_id }}
            </div>
            
            <div class="info-row">
                <strong>Kategori:</strong> {{ $ticket->category->name ?? '-' }}
            </div>
            
            <div class="info-row">
                <strong>Prioritas:</strong> 
                <span class="badge badge-{{ $ticket->priority }}">
                    {{ strtoupper($ticket->priority) }}
                </span>
            </div>
            
            <div class="info-row">
                {{-- PEMANGGILAN NAMA/PEMBUAT DIPERBAIKI DI SINI --}}
                <strong>Pembuat:</strong>
                {{ $ticket->user->name ?? 'Unknown' }}
                @if($ticket->user?->department)
                    ({{ $ticket->user->department->name }})
                @endif
            </div>
            
            <div class="info-row">
                <strong>Deskripsi:</strong><br>
                {!! nl2br(e($ticket->description)) !!}
            </div>
            
            <div class="info-row">
                <strong>Status:</strong> {{ strtoupper($ticket->status) }}
            </div>
            
            <div class="info-row">
                <strong>Waktu:</strong> {{ $ticket->created_at->format('d/m/Y H:i') }}
            </div>
            
            <a href="{{ url('/it/tickets') }}" class="btn">
                Lihat Detail Tiket
            </a>
        </div>
        
        <div class="footer">
            Email otomatis dari Sistem Ticketing KTU.<br>
            Mohon tidak membalas email ini.
        </div>
    </div>
</body>
</html>