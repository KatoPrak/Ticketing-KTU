<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketCreatedMail;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    /**
     * Display ticket list + handle AJAX reload.
     */
    public function index(Request $request)
    {
        $categories = Category::all();

        $query = Ticket::with(['category', 'user.department'])
            ->where('user_id', Auth::id());

        if ($request->status) $query->where('status', $request->status);
        if ($request->category_id) $query->where('category_id', $request->category_id);
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'like', "%{$request->search}%")
                  ->orWhere('ticket_id', 'like', "%{$request->search}%");
            });
        }

        // Active tickets with pagination
        $tickets = $query->whereNotIn('status', ['resolved', 'closed'])
                         ->orderBy('created_at', 'desc')
                         ->paginate(5);

        // Normalize/format each ticket
        $tickets->getCollection()->transform(function($ticket) {
            $ticket->attachments = json_decode($ticket->attachments, true) ?? [];
            $ticket->created_at_formatted = $ticket->created_at->timezone('Asia/Jakarta')->format('d M Y H:i');
            return $ticket;
        });

        // History tickets (resolved / closed)
        $historyTickets = Ticket::with(['category', 'user.department'])
            ->where('user_id', Auth::id())
            ->whereIn('status', ['resolved', 'closed'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($ticket) {
                $ticket->attachments = json_decode($ticket->attachments, true) ?? [];
                $ticket->created_at_formatted = $ticket->created_at->timezone('Asia/Jakarta')->format('d M Y H:i');
                return $ticket;
            });

        // If AJAX (live search / pagination), return JSON
        if ($request->ajax()) {
            return response()->json([
                'data' => $tickets->items(),
                'links' => $tickets->links('pagination::bootstrap-5')->render(),
                'current_page' => $tickets->currentPage(),
                'last_page' => $tickets->lastPage(),
                'total' => $tickets->total(),
            ]);
        }

        // Render view with both variables
        return view('staff.list-tiket', compact('categories', 'tickets', 'historyTickets'));
    }

    public function fetchDashboardTickets()
    {
        $tickets = Ticket::with('category')
            ->where('user_id', auth()->id())
            ->latest()
            ->take(3)
            ->get();

        return response()->json($tickets);
    }

    /**
     * Store a new ticket (called via AJAX).
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_id'   => 'required|exists:categories,id',
                'priority'      => 'required|string',
                'description'   => 'required|string',
                'attachments.*' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,heic,heif',
            ]);

            // Upload files (if any)
            $filePaths = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filePaths[] = $file->store('tickets', 'public');
                }
            }

            // Create new ticket
            $ticket = Ticket::create([
                'user_id'       => Auth::id(),
                'department_id' => Auth::user()->department_id,
                'category_id'   => $validated['category_id'],
                'priority'      => strtolower($validated['priority']),
                'description'   => $validated['description'],
                'attachments'   => json_encode($filePaths),
                'status'        => 'waiting',
            ]);

            // Generate custom ticket_id
            $ticket->ticket_id = 'T-KTU-' . str_pad($ticket->id, 4, '0', STR_PAD_LEFT);
            $ticket->save();

            // Load relations
            $ticket->load(['category', 'user.department']);

            // Send email (do not break AJAX if failed)
            try {
                $itEmail = env('IT_TEAM_EMAIL', 'irvanronaldi2@gmail.com');
                Mail::to($itEmail)->send(new TicketCreatedMail($ticket));

                Log::info('New ticket email sent', [
                    'ticket_id' => $ticket->ticket_id,
                    'to' => $itEmail,
                    'user' => Auth::user()->name,
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to send new ticket email', [
                    'ticket_id' => $ticket->ticket_id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Format response
            $responseData = [
                'id' => $ticket->id,
                'ticket_id' => $ticket->ticket_id,
                'category' => [
                    'id' => $ticket->category->id ?? null,
                    'name' => $ticket->category->name ?? '-',
                ],
                'user' => [
                    'id' => $ticket->user->id,
                    'name' => $ticket->user->name,
                    'department' => [
                        'id' => $ticket->user->department->id ?? null,
                        'name' => $ticket->user->department->name ?? '-',
                    ],
                ],
                'status' => ucfirst($ticket->status),
                'priority' => ucfirst($ticket->priority),
                'description' => $ticket->description,
                'attachments' => $filePaths,
                'created_at_formatted' => $ticket->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i'),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Ticket successfully created!',
                'ticket' => $responseData,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed!',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create new ticket', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Display ticket details (AJAX).
     */
    public function show($id)
    {
        try {
            $ticket = Ticket::with(['category', 'user.department'])
                ->where('user_id', Auth::id())
                ->findOrFail($id);

            // Decode attachments and format dates
            $ticket->attachments = json_decode($ticket->attachments, true) ?? [];
            $ticket->created_at_formatted = $ticket->created_at->timezone('Asia/Jakarta')->format('d M Y H:i');
            $ticket->updated_at_formatted = $ticket->updated_at->timezone('Asia/Jakarta')->format('d M Y H:i');

            return response()->json([
                'success' => true,
                'ticket' => [
                    'id' => $ticket->id,
                    'ticket_id' => $ticket->ticket_id,
                    'category' => [
                        'id' => $ticket->category->id ?? null,
                        'name' => $ticket->category->name ?? '-',
                    ],
                    'user' => [
                        'id' => $ticket->user->id,
                        'name' => $ticket->user->name,
                        'department' => [
                            'id' => $ticket->user->department->id ?? null,
                            'name' => $ticket->user->department->name ?? '-',
                        ],
                    ],
                    'status' => ucfirst($ticket->status),
                    'priority' => ucfirst($ticket->priority),
                    'description' => $ticket->description,
                    'attachments' => $ticket->attachments,
                    'created_at' => $ticket->created_at_formatted,
                    'updated_at' => $ticket->updated_at_formatted,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found.',
                'error' => $e->getMessage(),
            ], 404);
        }
    }
}
