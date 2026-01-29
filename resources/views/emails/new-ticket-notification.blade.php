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
        .badge-low {
            background: #2ECC71;
            color: white;
        }
        .badge-medium {
            background: #F1C40F;
            color: #000;
        }
        .badge-high {
            background: #E67E22;
            color: white;
        }
        .badge-urgent {
            background: #E74C3C;
            color: white;
        }
        .badge-critical {
            background: #900C3F;
            color: white;
        }
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
        @media only screen and (max-width: 620px) {
            body, .container {
                padding: 10px;
            }
            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin: 0;">🎫 New Ticket Received</h2>
        </div>

        <div class="content">
            <p>Hello IT Team,</p>
            <p>A new ticket has been created and requires your attention.</p>

            <div class="info-row"><strong>Ticket ID:</strong> {{ $ticket->ticket_id }}</div>
            <div class="info-row"><strong>Category:</strong> {{ $ticket->category->name ?? '-' }}</div>
            <div class="info-row">
                <strong>Priority:</strong>
                <span class="badge badge-{{ strtolower($ticket->priority) }}">{{ strtoupper($ticket->priority) }}</span>
            </div>
            <div class="info-row">
                <strong>Created By:</strong> {{ $ticket->user->name ?? 'Unknown' }}
                @if($ticket->user?->department)
                    ({{ $ticket->user->department->name }})
                @endif
            </div>
            <div class="info-row"><strong>Description:</strong><br>{!! nl2br(e($ticket->description)) !!}</div>
            <div class="info-row"><strong>Status:</strong> {{ strtoupper($ticket->status) }}</div>
            <div class="info-row"><strong>Created At:</strong> {{ $ticket->created_at->format('d/m/Y H:i') }}</div>

            <a href="{{ url('/it/tickets') }}" class="btn">View Ticket Details</a>
        </div>

        <div class="footer">
            This is an automated email from the KTU Ticketing System.<br>
            Please do not reply to this message.
        </div>
    </div>
</body>
</html>
