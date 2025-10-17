<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Category;
use App\Models\Department;
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
    $query = Ticket::with(['category', 'user.department'])
        ->where('user_id', auth()->id())
        ->latest();

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('ticket_id', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhereHas('category', fn($cat) => $cat->where('name', 'like', "%{$search}%"))
              ->orWhereHas('user.department', fn($dept) => $dept->where('name', 'like', "%{$search}%"));
        });
    }

    // kalau request AJAX, return JSON
    if ($request->ajax()) {
        $tickets = $query->get()->map(function($t) {
            return [
                'id' => $t->id,
                'ticket_id' => $t->ticket_id,
                'user' => [
                    'id' => $t->user->id,
                    'name' => $t->user->name,
                ],
                'department' => [
                    'id' => $t->user->department->id ?? null,
                    'name' => $t->user->department->name ?? '-',
                ],
                'category' => [
                    'id' => $t->category->id ?? null,
                    'name' => $t->category->name ?? '-',
                ],
                'priority' => ucfirst($t->priority),
                'status' => ucfirst($t->status),
                'created_at_formatted' => $t->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i'),
            ];
        });

        return response()->json(['tickets' => $tickets]);
    }

    // kalau bukan AJAX, render blade biasa
    $tickets = $query->paginate(5);
    $historyTickets = Ticket::with(['category', 'user.department'])
        ->where('user_id', auth()->id())
        ->whereIn('status', ['resolved', 'closed'])
        ->latest()
        ->take(5)
        ->get();

    return view('staff.list-tiket', compact('tickets', 'historyTickets'));
}

    /**
     * Fetch latest tickets for dashboard.
     */
    public function fetchDashboardTickets()
    {
        // ✅ pakai get() bukan paginate() untuk response JSON dashboard
        $tickets = Ticket::with(['category', 'user.department', 'department'])
            ->where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        return response()->json($tickets);
    }

    /**
     * Store a new ticket (AJAX).
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

        // Upload file attachments
        $filePaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filePaths[] = $file->store('tickets', 'public');
            }
        }

        $user = Auth::user();

        // Pastikan department_id tidak null
        $departmentId = $user->department_id ?? Department::first()?->id;
        if (!$departmentId) {
            return response()->json([
                'success' => false,
                'message' => 'No department found. Please assign a department to this user.',
            ], 400);
        }

        // Create ticket
        $ticket = Ticket::create([
            'user_id'       => $user->id,
            'department_id' => $departmentId,
            'category_id'   => $validated['category_id'],
            'priority'      => strtolower($validated['priority']),
            'description'   => $validated['description'],
            'attachments'   => json_encode($filePaths),
            'status'        => 'waiting',
        ]);

        // Generate custom ticket ID
        $ticket->ticket_id = 'T-KTU-' . str_pad($ticket->id, 4, '0', STR_PAD_LEFT);
        $ticket->save();

        // Load relations
        $ticket->load(['category', 'user.department', 'department']);

        // Ambil email tim IT dari .env
        $itEmails = explode(',', env('IT_TEAM_EMAILS', 'ferdinal.sukman@ktushipyard.com'));

        // Kirim email ke semua email tim IT
        try {
            Mail::to($itEmails)->send(new TicketCreatedMail($ticket));
        } catch (\Exception $e) {
            Log::warning('Email ticket gagal dikirim', ['error' => $e->getMessage()]);
        }

        // Response JSON
        return response()->json([
            'success' => true,
            'message' => 'Ticket successfully created!',
            'ticket' => [
                'id' => $ticket->id,
                'ticket_id' => $ticket->ticket_id,
                'category' => [
                    'id' => $ticket->category->id ?? null,
                    'name' => $ticket->category->name ?? '-',
                ],
                'department' => [
                    'id' => $ticket->department->id ?? null,
                    'name' => $ticket->department->name ?? '-',
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
            ],
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

        return response()->json([
            'success' => false,
            'message' => 'Failed to create ticket, please try again later.',
        ], 500);
    }
}

    /**
     * Display ticket details (AJAX).
     */
    public function show($id)
    {
        try {
            $ticket = Ticket::with(['category', 'user.department', 'department'])
                ->where('user_id', Auth::id())
                ->findOrFail($id);

            $ticket->attachments = json_decode($ticket->attachments, true) ?? [];
            $ticket->created_at_formatted = $ticket->created_at->timezone('Asia/Jakarta')->format('d M Y H:i');
            $ticket->updated_at_formatted = $ticket->updated_at->timezone('Asia/Jakarta')->format('d M Y H:i');

            return response()->json([
                'success' => true,
                'ticket'  => [
                    'id'         => $ticket->id,
                    'ticket_id'  => $ticket->ticket_id,
                    'category'   => [
                        'id'   => $ticket->category->id ?? null,
                        'name' => $ticket->category->name ?? '-',
                    ],
                    'department' => [
                        'id'   => $ticket->department->id ?? null,
                        'name' => $ticket->department->name ?? '-',
                    ],
                    'user'       => [
                        'id'         => $ticket->user->id,
                        'name'       => $ticket->user->name,
                        'department' => [
                            'id'   => $ticket->user->department->id ?? null,
                            'name' => $ticket->user->department->name ?? '-',
                        ],
                    ],
                    'status'     => ucfirst($ticket->status),
                    'priority'   => ucfirst($ticket->priority),
                    'description'=> $ticket->description,
                    'attachments'=> $ticket->attachments,
                    'created_at' => $ticket->created_at_formatted,
                    'updated_at' => $ticket->updated_at_formatted,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found.',
                'error'   => $e->getMessage(),
            ], 404);
        }
    }
}
