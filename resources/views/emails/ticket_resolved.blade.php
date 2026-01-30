<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f3f4f6; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .header { background-color: #4f46e5; color: white; padding: 25px; text-align: center; }
        .header h2 { margin: 0; font-size: 24px; font-weight: 700; }
        .content { padding: 30px; }
        .info-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .row { margin-bottom: 12px; display: flex; align-items: flex-start; }
        .label { font-weight: 600; color: #4b5563; min-width: 100px; display: inline-block; }
        .value { color: #111827; flex: 1; }
        
        /* Badges */
        .badge { display: inline-block; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: white; }
        .badge-resolved { background-color: #10b981; } /* Green for Resolved */
        .badge-closed { background-color: #6b7280; } /* Gray for Closed */
        
        .btn { display: inline-block; background-color: #4f46e5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: 600; margin-top: 10px; text-align: center; }
        .btn:hover { background-color: #4338ca; }
        
        .footer { background-color: #f9fafb; padding: 20px; text-align: center; font-size: 12px; color: #6b7280; border-top: 1px solid #e5e7eb; }
        
        @media (max-width: 600px) {
            .content { padding: 20px; }
            .header { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>✅ Ticket Resolved</h2>
        </div>

        <div class="content">
            <p style="margin-top: 0; font-size: 16px;">Dear <strong>{{ $ticket->user->name }}</strong>,</p>
            <p style="color: #4b5563;">Good news! Your support ticket has been marked as <strong>Resolved</strong>.</p>

            <div class="info-box">
                <div class="row">
                    <span class="label">Ticket ID:</span>
                    <span class="value" style="font-family: monospace; font-weight: 700;">{{ $ticket->ticket_id }}</span>
                </div>
                <div class="row">
                    <span class="label">Status:</span>
                    <span class="value">
                        <span class="badge badge-{{ $ticket->status == 'resolved' ? 'resolved' : 'closed' }}">
                            {{ strtoupper($ticket->status) }}
                        </span>
                    </span>
                </div>
                <div class="row">
                    <span class="label">Issue:</span>
                    <span class="value">{{ $ticket->description }}</span>
                </div>
                
                @if($ticket->resolution_notes)
                <div style="margin-top: 15px; border-top: 1px dashed #d1d5db; padding-top: 15px;">
                    <span class="label" style="display: block; margin-bottom: 5px;">Resolution Notes:</span>
                    <div class="value" style="background: white; padding: 10px; border-radius: 6px; border: 1px solid #e5e7eb; color: #374151;">
                        {!! nl2br(e($ticket->resolution_notes)) !!}
                    </div>
                </div>
                @endif
            </div>

            <p style="text-align: center; color: #4b5563; font-size: 14px;">If you have any further questions or if the issue persists, please let us know.</p>
            
            <p style="text-align: center; font-weight: 600;">We value your feedback! Please click below to rate our service.</p>

            <div style="text-align: center;">
                <a href="{{ route('staff.tickets.show', $ticket->id) }}" class="btn">View Ticket & Give Feedback</a>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} KTU Shipyard IT Support System.<br>This is an automated notification.</p>
        </div>
    </div>
</body>
</html>
