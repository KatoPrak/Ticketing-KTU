<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Resolved</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f3f4f6; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }

        /* ── HEADER ── */
        .header-logo-bar { background: white; padding: 16px 24px; display: flex; align-items: center; justify-content: center; border-bottom: 3px solid #c8181e; }
        .header-logo-bar img { height: 52px; width: auto; }
        .header-logo-bar .brand { margin-left: 14px; text-align: left; }
        .header-logo-bar .brand span { display: block; font-size: 18px; font-weight: 800; color: #1a2e5a; }
        .header-logo-bar .brand small { display: block; font-size: 11px; color: #c8181e; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; }
        .header-title { background: linear-gradient(135deg, #0a6640 0%, #10b981 100%); padding: 22px 20px; text-align: center; }
        .header-title h2 { margin: 0; font-size: 22px; font-weight: 700; color: white; }
        .header-title p { margin: 6px 0 0; font-size: 13px; color: rgba(255,255,255,0.8); }

        /* ── CONTENT ── */
        .content { padding: 30px; }
        .info-box { background: #f9fafb; border: 1px solid #e5e7eb; border-left: 4px solid #10b981; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .row { margin-bottom: 12px; display: flex; align-items: flex-start; font-size: 14px; }
        .label { font-weight: 600; color: #4b5563; min-width: 130px; display: inline-block; }
        .value { color: #111827; flex: 1; }
        .badge { display: inline-block; padding: 3px 9px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: white; }
        .badge-resolved { background-color: #10b981; }
        .badge-closed { background-color: #6b7280; }
        .btn { display: inline-block; background: linear-gradient(135deg, #1a2e5a, #2b4a9e); color: white !important; padding: 13px 28px; text-decoration: none; border-radius: 8px; font-weight: 700; margin-top: 10px; font-size: 14px; }

        /* ── FOOTER ── */
        .footer { background-color: #f9fafb; padding: 20px 24px; text-align: center; font-size: 12px; color: #6b7280; border-top: 1px solid #e5e7eb; }
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
            <h2>✅ Ticket Resolved</h2>
            <p>Your issue has been successfully handled</p>
        </div>

        {{-- ── CONTENT ── --}}
        <div class="content">
            <p style="margin-top:0; font-size:16px;">Hello <strong>{{ $ticket->user->name }}</strong>,</p>
            <p style="color:#4b5563;">Your IT support ticket has been marked as <strong>Resolved</strong>. Here is the summary:</p>

            <div class="info-box">
                <div class="row">
                    <span class="label">Ticket ID</span>
                    <span class="value" style="font-family:monospace; font-weight:700; color:#1a2e5a;">{{ $ticket->ticket_id }}</span>
                </div>
                <div class="row">
                    <span class="label">Status</span>
                    <span class="value">
                        <span class="badge badge-{{ $ticket->status == 'resolved' ? 'resolved' : 'closed' }}">
                            {{ strtoupper($ticket->status) }}
                        </span>
                    </span>
                </div>
                <div class="row">
                    <span class="label">Description</span>
                    <span class="value">{{ $ticket->description }}</span>
                </div>

                @if($ticket->resolution_notes)
                <div style="margin-top:15px; border-top:1px dashed #d1d5db; padding-top:15px;">
                    <span class="label" style="display:block; margin-bottom:6px;">💡 Resolution Notes</span>
                    <div class="value" style="background:white; padding:12px; border-radius:6px; border:1px solid #e5e7eb; color:#374151; font-size:13px;">
                        {!! nl2br(e($ticket->resolution_notes)) !!}
                    </div>
                </div>
                @endif
            </div>

            <p style="text-align:center; color:#4b5563; font-size:14px;">
                If the issue persists or if you have any further questions, please feel free to contact us again.
            </p>

            <div style="text-align:center; margin-top:20px;">
                <a href="{{ route('staff.tickets.show', $ticket->id) }}" class="btn">📋 View Ticket</a>
            </div>
        </div>

        {{-- ── FOOTER ── --}}
        <div class="footer">
            <img src="{{ config('app.url') }}/assets/image/logo-ktu.jpg" alt="KTU"><br>
            <span class="footer-brand">IT Support Team — KTU Shipyard</span><br>
            <span style="font-size:11px; color:#9ca3af; margin-top:6px; display:block;">
                This email was sent automatically. Please do not reply to this email.<br>
                &copy; {{ date('Y') }} KTU Shipyard IT Support System.
            </span>
        </div>
    </div>
</body>
</html>

