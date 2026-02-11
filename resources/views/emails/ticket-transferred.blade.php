<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Transferred</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .email-container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .email-header { background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; padding: 30px 20px; text-align: center; }
        .email-header h1 { margin: 0; font-size: 24px; font-weight: 600; }
        .email-header .icon { font-size: 48px; margin-bottom: 10px; }
        .email-body { padding: 30px 20px; }
        .ticket-info { background-color: #f8f9fa; border-left: 4px solid #17a2b8; padding: 20px; margin: 20px 0; border-radius: 4px; }
        .ticket-info h2 { margin: 0 0 15px 0; color: #17a2b8; font-size: 18px; }
        .info-row { display: flex; padding: 8px 0; border-bottom: 1px solid #e9ecef; }
        .info-label { font-weight: 600; color: #495057; min-width: 140px; }
        .info-value { color: #212529; flex: 1; }
        .transfer-box { background-color: #fff3cd; border: 1px solid #ffeeba; color: #856404; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .button { display: inline-block; background-color: #007bff; color: white; text-decoration: none; padding: 12px 30px; border-radius: 4px; margin: 20px 0; font-weight: 600; }
        .email-footer { background-color: #f8f9fa; padding: 20px; text-align: center; color: #6c757d; font-size: 12px; border-top: 1px solid #dee2e6; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <div class="icon">🚚</div>
            <h1>Ticket Transfer Masuk</h1>
        </div>
        <div class="email-body">
            <p>Halo Tim IT <strong>{{ $toRegion->name }}</strong>,</p>
            <p>Ada ticket baru yang ditransfer ke regional Anda oleh <strong>{{ $transferredBy }}</strong>.</p>

            <div class="transfer-box">
                <strong>🔄 Catatan Transfer:</strong><br>
                {{ $note ?? 'Tidak ada catatan.' }}
            </div>

            <div class="ticket-info">
                <h2>📋 Informasi Ticket</h2>
                <div class="info-row"><div class="info-label">Ticket ID:</div><div class="info-value"><strong>{{ $ticket->ticket_id ?? '#'.$ticket->id }}</strong></div></div>
                <div class="info-row"><div class="info-label">Judul:</div><div class="info-value">{{ $ticket->title ?? '-' }}</div></div>
                <div class="info-row"><div class="info-label">Deskripsi:</div><div class="info-value">{{ \Illuminate\Support\Str::limit($ticket->description, 100) }}</div></div>
                <div class="info-row"><div class="info-label">Pelapor:</div><div class="info-value">{{ $ticket->user->name ?? 'Unknown' }}</div></div>
                <div class="info-row"><div class="info-label">Asal Regional:</div><div class="info-value">{{ $fromRegion->name }}</div></div>
            </div>

            <div style="text-align: center;">
                <a href="{{ config('app.url') }}" class="button">Lihat & Proses Ticket</a>
            </div>
        </div>
        <div class="email-footer">
            <p>IT Support Team - KTU Shipyard</p>
        </div>
    </div>
</body>
</html>
