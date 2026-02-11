<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Closed</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-header .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .email-body {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 16px;
            color: #333;
            margin-bottom: 20px;
        }
        .ticket-info {
            background-color: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .ticket-info h2 {
            margin: 0 0 15px 0;
            color: #28a745;
            font-size: 18px;
        }
        .info-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
            min-width: 140px;
        }
        .info-value {
            color: #212529;
            flex: 1;
        }
        .solution-box {
            background-color: #e7f5ff;
            border: 1px solid #339af0;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
        }
        .solution-box h3 {
            margin: 0 0 10px 0;
            color: #1971c2;
            font-size: 16px;
        }
        .solution-box p {
            margin: 0;
            color: #495057;
            line-height: 1.6;
        }
        .status-badge {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #dee2e6;
        }
        .email-footer p {
            margin: 5px 0;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 4px;
            margin: 20px 0;
            font-weight: 600;
        }
        .button:hover {
            background-color: #0056b3;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 0;
                border-radius: 0;
            }
            .info-row {
                flex-direction: column;
            }
            .info-label {
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="icon">✅</div>
            <h1>Ticket Telah Diselesaikan</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            <div class="greeting">
                <p>Halo <strong>{{ $ticket->user->name ?? $ticket->customer->name ?? 'User' }}</strong>,</p>
                <p>Kami informasikan bahwa ticket Anda telah <strong>diselesaikan</strong> oleh tim IT kami.</p>
            </div>

            <!-- Ticket Information -->
            <div class="ticket-info">
                <h2>📋 Informasi Ticket</h2>
                
                <div class="info-row">
                    <div class="info-label">Ticket ID:</div>
                    <div class="info-value"><strong>{{ $ticket->ticket_id ?? '#'.$ticket->id }}</strong></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Judul:</div>
                    <div class="info-value">{{ $ticket->title ?? '-' }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Deskripsi:</div>
                    <div class="info-value">{{ Str::limit($ticket->description, 100) }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Lokasi:</div>
                    <div class="info-value">
                        @if($ticket->user && $ticket->user->location)
                            {{ $ticket->user->location->name }}
                        @else
                            -
                        @endif
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Tanggal Lapor:</div>
                    <div class="info-value">{{ $ticket->created_at->format('d M Y, H:i') }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Tanggal Selesai:</div>
                    <div class="info-value">{{ now()->format('d M Y, H:i') }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Ditangani Oleh:</div>
                    <div class="info-value">{{ $closedBy ?? 'IT Team' }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Status:</div>
                    <div class="info-value">
                        <span class="status-badge">CLOSED</span>
                    </div>
                </div>
            </div>

            <!-- Solution (if provided) -->
            @if($solution)
            <div class="solution-box">
                <h3>💡 Solusi & Penanganan</h3>
                <p>{{ $solution }}</p>
            </div>
            @endif

            <!-- Message -->
            <p style="margin-top: 20px; color: #495057; line-height: 1.6;">
                Terima kasih telah melaporkan masalah ini. Jika Anda masih mengalami kendala atau memiliki pertanyaan lebih lanjut, 
                jangan ragu untuk menghubungi kami atau membuat ticket baru.
            </p>

            <!-- Button (optional) -->
            <div style="text-align: center;">
                <a href="{{ config('app.url') }}" class="button">Lihat Dashboard</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p><strong>IT Support Team - KTU Shipyard</strong></p>
            <p>Email: {{ config('mail.from.address') }}</p>
            <p style="font-size: 12px; color: #adb5bd; margin-top: 10px;">
                Email ini dikirim secara otomatis. Mohon tidak membalas email ini.
            </p>
        </div>
    </div>
</body>
</html>
