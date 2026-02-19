<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incoming Ticket Transfer</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }

        /* ── HEADER ── */
        .header-logo-bar { background: white; padding: 16px 24px; display: flex; align-items: center; justify-content: center; border-bottom: 3px solid #c8181e; }
        .header-logo-bar img { height: 52px; width: auto; }
        .header-logo-bar .brand { margin-left: 14px; text-align: left; }
        .header-logo-bar .brand span { display: block; font-size: 18px; font-weight: 800; color: #1a2e5a; }
        .header-logo-bar .brand small { display: block; font-size: 11px; color: #c8181e; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; }
        .header-title { background: linear-gradient(135deg, #117a8b 0%, #17a2b8 100%); padding: 22px 20px; text-align: center; }
        .header-title h1 { margin: 0; font-size: 22px; font-weight: 700; color: white; }
        .header-title p { margin: 6px 0 0; font-size: 13px; color: rgba(255,255,255,0.8); }

        /* ── BODY ── */
        .body { padding: 30px; }
        .ticket-info { background-color: #f8f9fa; border-left: 4px solid #17a2b8; padding: 20px; margin: 20px 0; border-radius: 6px; }
        .ticket-info h2 { margin: 0 0 15px 0; color: #117a8b; font-size: 16px; }
        .info-row { display: flex; padding: 8px 0; border-bottom: 1px solid #e9ecef; font-size: 14px; }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-weight: 600; color: #495057; min-width: 150px; }
        .info-value { color: #212529; flex: 1; }
        .transfer-box { background-color: #fff8e1; border: 1px solid #ffe082; border-left: 4px solid #ffc107; color: #795548; padding: 15px; margin-bottom: 20px; border-radius: 6px; }
        .transfer-box strong { color: #5d4037; }
        .btn { display: inline-block; background: linear-gradient(135deg, #1a2e5a, #2b4a9e); color: white !important; text-decoration: none; padding: 13px 28px; border-radius: 8px; margin: 20px 0; font-weight: 700; font-size: 14px; }

        /* ── FOOTER ── */
        .footer { background-color: #f8f9fa; padding: 20px 24px; text-align: center; color: #6c757d; font-size: 12px; border-top: 1px solid #dee2e6; }
        .footer img { height: 28px; margin-bottom: 8px; opacity: 0.7; }
        .footer .footer-brand { font-weight: 700; color: #1a2e5a; font-size: 13px; }
    </style>
</head>
<body>
    <div class="container">

        {{-- ── HEADER ── --}}
        <div class="header-logo-bar">
            <img src="{{ config('app.url') }}/assets/image/logo-ktu.jpg" alt="KTU Logo">
            <div class="brand">
                <span>KTU Shipyard</span>
                <small>IT Support System</small>
            </div>
        </div>
        <div class="header-title">
            <h1>🚚 Incoming Ticket Transfer</h1>
            <p>A ticket has been transferred to your region</p>
        </div>

        {{-- ── BODY ── --}}
        <div class="body">
            <p>Hello <strong>IT Team {{ $toRegion->name }}</strong>,</p>
            <p style="color:#495057;">A ticket has been transferred to your region by <strong>{{ $transferredBy }}</strong>. Please follow up immediately.</p>

            @if($note)
            <div class="transfer-box">
                <strong>🔄 Transfer Note:</strong><br>
                <span style="margin-top:6px; display:block;">{{ $note }}</span>
            </div>
            @endif

            <div class="ticket-info">
                <h2>📋 Ticket Information</h2>
                <div class="info-row">
                    <div class="info-label">Ticket ID</div>
                    <div class="info-value"><strong style="font-family:monospace; color:#1a2e5a;">{{ $ticket->ticket_id ?? '#'.$ticket->id }}</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Description</div>
                    <div class="info-value">{{ \Illuminate\Support\Str::limit($ticket->description, 120) }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Requester</div>
                    <div class="info-value">{{ $ticket->user->name ?? 'Unknown' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">From Region</div>
                    <div class="info-value">📌 {{ $fromRegion->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">To Region</div>
                    <div class="info-value">📍 <strong>{{ $toRegion->name }}</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Transferred By</div>
                    <div class="info-value">{{ $transferredBy }}</div>
                </div>
            </div>

            <div style="text-align:center;">
                <a href="{{ config('app.url') }}/it/tickets" class="btn">🔍 View &amp; Process Ticket</a>
            </div>
        </div>

        {{-- ── FOOTER ── --}}
        <div class="footer">
            <img src="{{ config('app.url') }}/assets/image/logo-ktu.jpg" alt="KTU"><br>
            <span class="footer-brand">IT Support Team — KTU Shipyard</span><br>
            <span>Email: {{ config('mail.from.address') }}</span><br>
            <span style="font-size:11px; color:#adb5bd; margin-top:6px; display:block;">
                This email was sent automatically. Please do not reply to this email.<br>
                &copy; {{ date('Y') }} KTU Shipyard IT Support System.
            </span>
        </div>
    </div>
</body>
</html>

