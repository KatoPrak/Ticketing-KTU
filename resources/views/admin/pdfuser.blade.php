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
        }

        th {
            padding: 6px 3px;
            text-align: left;
            vertical-align: middle;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            border-right: 1px solid rgba(255, 255, 255, 0.2);
        }

        th:last-child {
            border-right: none;
        }

        td {
            padding: 5px 3px;
            border-bottom: 1px solid #e1e7f0;
            text-align: left;
            vertical-align: top;
            font-size: 7px;
            line-height: 1.3;
            /* ✅ KEY: Allow text wrapping */
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }

        tbody tr:nth-child(even) { 
            background: #f8fafb; 
        }

        /* ===== COLUMN WIDTHS - OPTIMIZED FOR READABILITY ===== */
        th:nth-child(1), td:nth-child(1) { width: 5%; text-align: center; }    /* ID */
        th:nth-child(2), td:nth-child(2) { width: 12%; }                        /* Description */
        th:nth-child(3), td:nth-child(3) { width: 7%; }                         /* User */
        th:nth-child(4), td:nth-child(4) { width: 7%; }                         /* Department */
        th:nth-child(5), td:nth-child(5) { width: 7%; }                         /* Category */
        th:nth-child(6), td:nth-child(6) { width: 5%; text-align: center; }    /* Priority */
        th:nth-child(7), td:nth-child(7) { width: 5%; text-align: center; }    /* Status */
        th:nth-child(8), td:nth-child(8) { width: 5%; text-align: center; }    /* Rating */
        th:nth-child(9), td:nth-child(9) { width: 14%; }                        /* Comment - MORE SPACE */
        th:nth-child(10), td:nth-child(10) { width: 12%; }                      /* Remark */
        th:nth-child(11), td:nth-child(11) { width: 7%; font-size: 6.5px; }    /* Report Date */
        th:nth-child(12), td:nth-child(12) { width: 7%; font-size: 6.5px; }    /* Response Date */
        th:nth-child(13), td:nth-child(13) { width: 7%; font-size: 6.5px; }    /* Resolved Date */

        /* ===== BADGES ===== */
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 6px;
            font-weight: bold;
            color: #fff;
            text-transform: uppercase;
            white-space: nowrap;
        }

        /* Status Badges */
        .waiting { background-color: #3498db; }
        .in_progress { background-color: #f39c12; }
        .pending { background-color: #e67e22; }
        .resolved { background-color: #27ae60; }
        .closed { background-color: #95a5a6; }
        .open { background-color: #5dade2; }

        /* Priority Badges */
        .priority-critical { background-color: #8b0000; }
        .priority-urgent { background-color: #c0392b; }
        .priority-high { background-color: #e74c3c; }
        .priority-medium { background-color: #f39c12; }
        .priority-low { background-color: #27ae60; }

        /* ===== RATING STARS ===== */
        .rating-stars {
            color: #f39c12;
            font-size: 9px;
            line-height: 1.1;
            text-align: center;
            letter-spacing: 1px;
        }

        .no-rating {
            color: #999;
            font-style: italic;
            font-size: 6px;
            text-align: center;
            display: block;
            padding: 3px 0;
        }

        /* ===== TEXT CELLS - RESPONSIVE ===== */
        .text-wrap {
            display: block;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
            max-height: none;
        }

        .comment-text {
            font-size: 7px;
            color: #555;
            line-height: 1.3;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }

        .no-comment {
            color: #999;
            font-style: italic;
            text-align: center;
            font-size: 6px;
        }

        /* ===== DATE FORMATTING - WITH SECONDS ===== */
        .date-only {
            display: block;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 1px;
            font-size: 6.5px;
        }

        .time-only {
            display: block;
            font-size: 6px;
            color: #64748b;
            font-family: 'Courier New', monospace;
        }

        .no-date {
            color: #999;
            font-style: italic;
            font-size: 6px;
        }

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

        /* ===== TABLE ROW - PREVENT PAGE BREAK ===== */
        tbody tr {
            page-break-inside: avoid;
        }

        /* ===== ENSURE PROPER SPACING BEFORE FOOTER ===== */
        .meta-line {
            font-size: 7px;
            color: #777;
            margin-top: 15px;
            margin-bottom: 20px;
            text-align: center;
            font-style: italic;
            page-break-after: avoid;
        }

        /* ===== NO DATA ===== */
        .no-data {
            text-align: center;
            padding: 25px;
            color: #999;
            font-style: italic;
        }

        /* ===== FEEDBACK SUMMARY ===== */
        .feedback-summary {
            background: #fff9e6;
            padding: 7px 10px;
            border-left: 3px solid #f39c12;
            font-size: 7px;
            color: #333;
            margin-bottom: 10px;
        }

        .feedback-summary strong {
            color: #f39c12;
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
        This document provides a comprehensive summary of all IT support tickets recorded during the specified period. 
        It includes detailed information about issue categories, assigned users, departments, priority levels, current statuses, 
        resolution markings, <strong>user ratings, and feedback comments</strong>.
        <strong>Total Tickets: {{ count($tickets) }}</strong>
    </div>

    @php
        $withFeedback = $tickets->filter(function($ticket) {
            return $ticket->feedback !== null;
        })->count();
        $avgRating = $tickets->filter(function($ticket) {
            return $ticket->feedback !== null;
        })->avg(function($ticket) {
            return $ticket->feedback->rating;
        });
    @endphp

    @if($withFeedback > 0)
    <div class="feedback-summary">
        <strong>Feedback Statistics:</strong> 
        {{ $withFeedback }} tickets have feedback ({{ round(($withFeedback / count($tickets)) * 100) }}%) | 
        Average Rating: <strong>{{ number_format($avgRating, 1) }} / 5.0</strong>
    </div>
    @endif

    <h3 class="section-title">Ticket List with Feedback</h3>

    @if(count($tickets) > 0)
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Description</th>
                <th>User</th>
                <th>Dept</th>
                <th>Category</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Rating</th>
                <th>Comment</th>
                <th>Remark</th>
                <th>Report Date</th>
                <th>Response Date</th>
                <th>Resolved Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tickets as $ticket)
                <tr>
                    {{-- ID --}}
                    <td><strong>{{ $ticket->ticket_id ?? '#TK' . str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }}</strong></td>
                    
                    {{-- Description - WITH WORD WRAP --}}
                    <td>
                        <span class="text-wrap">
                            {{ $ticket->description ?? 'No description' }}
                        </span>
                    </td>
                    
                    {{-- User --}}
                    <td>{{ $ticket->user->name ?? 'N/A' }}</td>
                    
                    {{-- Department --}}
                    <td>{{ $ticket->user->department->name ?? 'N/A' }}</td>
                    
                    {{-- Category --}}
                    <td>{{ $ticket->category->name ?? 'N/A' }}</td>
                    
                    {{-- Priority Badge --}}
                    <td>
                        <span class="badge priority-{{ strtolower($ticket->priority ?? 'low') }}">
                            {{ strtoupper(substr($ticket->priority ?? 'low', 0, 3)) }}
                        </span>
                    </td>
                    
                    {{-- Status Badge --}}
                    <td>
                        <span class="badge {{ strtolower(str_replace(' ', '_', $ticket->status)) }}">
                            {{ strtoupper(substr(str_replace('_', ' ', $ticket->status), 0, 3)) }}
                        </span>
                    </td>
                    
                    {{-- ⭐ RATING --}}
                    <td>
                        @if($ticket->feedback)
                            <div class="rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    {{ $i <= $ticket->feedback->rating ? '★' : '☆' }}
                                @endfor
                            </div>
                        @else
                            <span class="no-rating">No rating</span>
                        @endif
                    </td>

                    {{-- 💬 COMMENT - WITH WORD WRAP --}}
                    <td>
                        @if($ticket->feedback && $ticket->feedback->comment)
                            <div class="comment-text">
                                {{ $ticket->feedback->comment }}
                            </div>
                        @else
                            <span class="no-comment">-</span>
                        @endif
                    </td>

                    {{-- REMARK - WITH WORD WRAP --}}
                    <td>
                        @if($ticket->resolution_notes)
                            <span class="text-wrap">
                                {{ $ticket->resolution_notes }}
                            </span>
                        @else
                            <span class="no-comment">-</span>
                        @endif
                    </td>

                    {{-- ✅ REPORT DATE = created_at (WITH SECONDS) --}}
                    <td>
                        <span class="date-only">{{ $ticket->created_at->format('d/m/Y') }}</span>
                        <span class="time-only">{{ $ticket->created_at->format('H:i:s') }}</span>
                    </td>

                    {{-- ✅ RESPONSE DATE = updated_at (WITH SECONDS) --}}
                    <td>
                        @if($ticket->updated_at)
                            <span class="date-only">{{ $ticket->updated_at->format('d/m/Y') }}</span>
                            <span class="time-only">{{ $ticket->updated_at->format('H:i:s') }}</span>
                        @else
                            <span class="no-date">Not yet</span>
                        @endif
                    </td>

                    {{-- ✅ RESOLVED/CLOSED DATE = resolved_at (WITH SECONDS) --}}
                    <td>
                        @if($ticket->resolved_at)
                            <span class="date-only">{{ \Carbon\Carbon::parse($ticket->resolved_at)->format('d/m/Y') }}</span>
                            <span class="time-only">{{ \Carbon\Carbon::parse($ticket->resolved_at)->format('H:i:s') }}</span>
                        @else
                            <span class="no-date">Pending</span>
                        @endif
                    </td>
                </tr>
            @endforeach
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