<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>IT Support Ticket Report</title>
    <style>
        /* ===== BASE - LANDSCAPE ===== */
        @page {
            size: A4 landscape;
            margin: 12mm 8mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            color: #2c3e50;
            margin: 0;
            padding: 0;
            line-height: 1.3;
        }

        h1, h2, h3, h4, p { 
            margin: 0; 
            padding: 0; 
        }

        /* ===== HEADER ===== */
        header {
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #003366;
        }

        .header-content {
            display: table;
            width: 100%;
        }

        .header-logo {
            display: table-cell;
            width: 50px;
            vertical-align: middle;
        }

        .header-logo img {
            width: 45px;
            height: auto;
        }

        .header-info {
            display: table-cell;
            vertical-align: middle;
            padding-left: 12px;
        }

        .header-info h2 {
            font-size: 15px;
            color: #003366;
            margin-bottom: 3px;
            font-weight: bold;
        }

        .header-info p {
            font-size: 8px;
            color: #555;
            margin: 2px 0;
        }

        .header-info .period {
            font-size: 9px;
            font-weight: bold;
            color: #003366;
            margin-top: 4px;
        }

        /* ===== SUMMARY BOX ===== */
        .report-summary {
            background: #f0f4f8;
            padding: 8px 12px;
            border-left: 3px solid #003366;
            font-size: 8px;
            color: #333;
            margin-bottom: 12px;
        }

        .report-summary strong { 
            color: #003366; 
            font-weight: bold;
        }

        /* ===== SECTION TITLE ===== */
        h3.section-title {
            color: #003366;
            font-size: 10px;
            letter-spacing: 0.3px;
            margin-bottom: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* ===== TABLE - RESPONSIVE WITH WORD WRAP ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            table-layout: fixed;
            margin-bottom: 12px;
        }

        thead {
            background: #003366;
            color: white;
            position: sticky;
            top: 0;
        }

        th {
            padding: 5px 2px;
            text-align: left;
            vertical-align: middle;
            font-size: 6.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.1px;
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
            line-height: 1.2;
        }

        th:last-child {
            border-right: none;
        }

        td {
            padding: 4px 2px;
            border-bottom: 1px solid #e1e7f0;
            text-align: left;
            vertical-align: top;
            font-size: 6.5px;
            line-height: 1.3;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }

        tbody tr:nth-child(even) { 
            background: #f8fafb; 
        }

        tbody tr {
            page-break-inside: avoid;
        }

        /* ===== COLUMN WIDTHS - 12 COLUMNS ===== */
        th:nth-child(1), td:nth-child(1) { width: 4%; text-align: center; }    /* ID */
        th:nth-child(2), td:nth-child(2) { width: 8%; }                         /* Location */
        th:nth-child(3), td:nth-child(3) { width: 7%; }                         /* User */
        th:nth-child(4), td:nth-child(4) { width: 8%; }                         /* PIC */
        th:nth-child(5), td:nth-child(5) { width: 6%; }                         /* Category */
        th:nth-child(6), td:nth-child(6) { width: 10%; }                        /* Description */
        th:nth-child(7), td:nth-child(7) { width: 4%; text-align: center; }    /* Priority */
        th:nth-child(8), td:nth-child(8) { width: 4%; text-align: center; }    /* Status */
        th:nth-child(9), td:nth-child(9) { width: 7%; text-align: center; }    /* Report Date */
        th:nth-child(10), td:nth-child(10) { width: 7%; text-align: center; }  /* Response Date */
        th:nth-child(11), td:nth-child(11) { width: 7%; text-align: center; }  /* Resolved/Closed */
        th:nth-child(12), td:nth-child(12) { width: 22%; }                     /* Marking */







        /* ===== FOOTER ===== */
        footer {
            margin-top: 15px;
            text-align: center;
            font-size: 7px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 6px;
            page-break-inside: avoid;
        }

        /* ===== PREVENT CONTENT OVERLAP ===== */
        main {
            margin-bottom: 30px;
            padding-bottom: 20px;
        }



        /* ===== ENSURE PROPER SPACING BEFORE FOOTER ===== */
        .meta-line {
            font-size: 7px;
            color: #777;
            margin-top: 15px;
            margin-bottom: 20px;
            text-align: center;
            font-style: italic;
        }

        /* ===== NO DATA ===== */
        .no-data {
            text-align: center;
            padding: 15px 10px;
            color: #999;
            font-style: italic;
            font-size: 7px;
        }



        /* ===== TICKET ID ===== */
        .ticket-id {
            font-weight: 700;
            color: #003366;
            white-space: normal;
            font-family: 'Courier New', monospace;
            font-size: 6px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* ===== DESCRIPTION ===== */
        .ticket-desc {
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
            color: #334155;
            line-height: 1.25;
            font-size: 6.5px;
            max-height: 40px;
            overflow: hidden;
        }

        /* ===== DATE TIME CELL ===== */
        .datetime-cell {
            display: block;
            font-size: 6px;
            line-height: 1.3;
        }

        .date-part {
            font-weight: 600;
            color: #1e293b;
            font-size: 6px;
            display: block;
            margin-bottom: 0.5px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .time-part {
            font-size: 5.5px;
            color: #64748b;
            font-family: 'Courier New', monospace;
            display: block;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* ===== BADGE CUSTOM ===== */
        .badge-custom {
            display: inline-block;
            padding: 1px 3px;
            border-radius: 2px;
            font-size: 5.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.1px;
            white-space: normal;
            word-wrap: break-word;
            overflow-wrap: break-word;
            line-height: 1.1;
            max-width: 100%;
        }

        /* Priority Badge Colors */
        .badge-critical {
            background-color: #8b0000;
            color: white;
        }

        .badge-high {
            background-color: #e74c3c;
            color: white;
        }

        .badge-medium {
            background-color: #f39c12;
            color: white;
        }

        .badge-low {
            background-color: #27ae60;
            color: white;
        }

        /* Status Badge Colors */
        .badge-open {
            background-color: #3b82f6;
            color: white;
        }

        .badge-in-progress {
            background-color: #f59e0b;
            color: white;
        }

        .badge-resolved {
            background-color: #10b981;
            color: white;
        }

        .badge-pending {
            background-color: #f97316;
            color: white;
        }

        .badge-waiting {
            background-color: #06b6d4;
            color: white;
        }

        .badge-closed {
            background-color: #64748b;
            color: white;
        }

        .badge-transfer {
            background-color: #6366f1;
            color: white;
            font-size: 5px;
            padding: 1px 3px;
            border-radius: 2px;
            margin-top: 2px;
            display: inline-block;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* ===== MARKING CELL ===== */
        .marking-cell {
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
            color: #475569;
            font-size: 6.5px;
            line-height: 1.25;
            max-height: 50px;
            overflow: hidden;
        }

        .text-muted-custom {
            color: #94a3b8;
            font-style: italic;
            font-size: 6px;
        }

        .no-date {
            color: #999;
            font-style: italic;
            font-size: 6px;
            display: block;
            text-align: center;
        }
    </style>
</head>
<body>

<header>
    <div class="header-content">
        <div class="header-logo">
            <img src="{{ public_path('assets/image/logo-ktu.jpg') }}" alt="Company Logo">
        </div>
        <div class="header-info">
            <h2>PT KTU Shipyard</h2>
            <p>IT Department — Monthly Ticket Report with Feedback</p>
            @if(isset($selectedRegion))
                <p style="color: #4f46e5; font-weight: bold; font-size: 10px; margin-top: 2px;">Region: {{ $selectedRegion->name }}</p>
            @endif
            <p class="period">Period: 
                @if(request('year') && request('month'))
                    {{ DateTime::createFromFormat('!m', request('month'))->format('F') }} {{ request('year') }}
                @elseif(request('year'))
                    {{ request('year') }}
                @else
                    {{ now()->format('F Y') }}
                @endif
            </p>
        </div>
    </div>
</header>
<main>
    <div class="report-summary">
        This document provides a comprehensive summary of all IT support tickets recorded 
        @if(isset($selectedRegion)) for <strong>{{ $selectedRegion->name }}</strong> @endif 
        during the specified period. 
        It includes detailed information about issue categories, assigned users, departments, priority levels, current statuses, and resolution markings.
        <strong>Total Tickets: {{ count($tickets) }}</strong>
    </div>

    <h3 class="section-title">Ticket List</h3>

    @if(count($tickets) > 0)
    <table>
        <thead>
            <tr>
                <th>Tiket ID</th>
                <th>Location</th>
                <th>User</th>
                <th>PIC</th>
                <th>Category</th>
                <th>Description</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Report Date</th>
                <th>Response Date</th>
                <th>Resolved/Closed</th>
                <th>Marking</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $ticket)
                <tr>
                    <td>
                        <span class="ticket-id">{{ $ticket->ticket_id }}</span>
                        @if($ticket->transferLogs->count() > 0)
                            <div class="badge-transfer">Transferred</div>
                        @endif
                    </td>
                    <td>
                        {{ $ticket->user->location->name ?? 'Unknown' }}
                    </td>
                    <td>{{ $ticket->user->name ?? 'N/A' }}</td>
                    <td style="color: #003366; font-weight: bold;">
                        {{ $ticket->assignedTo->name ?? 'Unassigned' }}
                    </td>
                    <td>{{ $ticket->category->name ?? 'N/A' }}</td>
                    <td>
                        <div class="ticket-desc">
                            {{ $ticket->description }}
                        </div>
                    </td>
                    <td>
                        <span class="badge-custom
                            @if($ticket->priority === 'critical') badge-critical
                            @elseif($ticket->priority === 'urgent' || $ticket->priority === 'high') badge-high
                            @elseif($ticket->priority === 'medium') badge-medium
                            @else badge-low @endif">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge-custom
                            @if($ticket->status === 'in_progress') badge-in-progress
                            @elseif($ticket->status === 'resolved') badge-resolved
                            @elseif($ticket->status === 'pending') badge-pending
                            @elseif($ticket->status === 'waiting') badge-waiting
                            @elseif($ticket->status === 'closed') badge-closed
                            @else badge-open @endif">
                            {{ str_replace('_', ' ', ucfirst($ticket->status)) }}
                        </span>
                    </td>
                    <td>
                        <div class="datetime-cell">
                            <span class="date-part">{{ $ticket->created_at->format('d M Y') }}</span>
                            <span class="time-part">{{ $ticket->created_at->format('H:i:s') }}</span>
                        </div>
                    </td>
                    <td>
                        @if($ticket->updated_at)
                            <div class="datetime-cell">
                                <span class="date-part">{{ $ticket->updated_at->format('d M Y') }}</span>
                                <span class="time-part">{{ $ticket->updated_at->format('H:i:s') }}</span>
                            </div>
                        @else
                            <span class="no-date">Not yet</span>
                        @endif
                    </td>
                    <td>
                        @if($ticket->resolved_at)
                            <div class="datetime-cell">
                                <span class="date-part">{{ \Carbon\Carbon::parse($ticket->resolved_at)->format('d M Y') }}</span>
                                <span class="time-part">{{ \Carbon\Carbon::parse($ticket->resolved_at)->format('H:i:s') }}</span>
                            </div>
                        @else
                            <span class="no-date">Pending</span>
                        @endif
                    </td>
                    <td>
                        @if($ticket->resolution_notes)
                            <div class="marking-cell">
                                {{ $ticket->resolution_notes }}
                            </div>
                        @else
                            <span class="text-muted-custom">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12">
                        <div class="no-data">
                            <p>No tickets found</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @else
    <div class="no-data">
        No tickets found for the selected period.
    </div>
    @endif

    <div class="meta-line">
        Confidential Report — For Internal Use Only — Generated: {{ now()->format('d F Y, H:i:s') }}
    </div>
</main>

<footer>
    Generated by IT Support System | {{ now()->format('d M Y H:i:s') }}
</footer>

<script type="text/php">
    if (isset($pdf)) {
        $pdf->page_script('
            $font = $fontMetrics->get_font("DejaVu Sans", "normal");
            $size = 7;
            $pageText = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
            $y = 575;
            $x = 755;
            $pdf->text($x, $y, $pageText, $font, $size);
        ');
    }
</script>

</body>
</html>