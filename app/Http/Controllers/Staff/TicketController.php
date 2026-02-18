<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketFeedback;
use App\Models\Category;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketCreatedMail;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class TicketController extends Controller
{
    /**
     * Display ticket list + handle AJAX reload.
     */
    public function index(Request $request)
    {
        // Query untuk ACTIVE TICKETS (bukan closed/resolved)
        $query = Ticket::with(['category', 'user.department'])
            ->where('user_id', auth()->id())
            ->whereNotIn('status', ['resolved', 'closed'])
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

        // ✅ AJAX: Return JSON menggunakan accessor dari Model
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
                    
                    // ✅ Menggunakan accessor dari Model (auto NULL-safe)
                    'created_at_formatted' => $t->created_at_formatted,
                    'updated_at_formatted' => $t->updated_at_formatted,
                    'resolved_at_formatted' => $t->resolved_at_formatted,
                    'response_date' => $t->response_date,
                ];
            });

            return response()->json(['tickets' => $tickets]);
        }

        // Render Blade
        $tickets = $query->paginate(10);

        // HISTORY TICKETS dengan Feedback Relation
        $historyTickets = Ticket::with(['user.department', 'category', 'feedback'])
            ->where('user_id', Auth::id())
            ->whereIn('status', ['closed', 'resolved'])
            ->orderBy('resolved_at','desc')
            ->paginate(10, ['*'], 'history_page');

        return view('staff.list-tiket', compact('tickets', 'historyTickets'));
    }

    /**
     * Delete Ticket
     */
    public function destroy($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            
            if ($ticket->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to delete this ticket.'
                ], 403);
            }
            
            $ticketNumber = $ticket->ticket_id;
            $ticket->delete();
            
            return response()->json([
                'success' => true,
                'message' => "Ticket {$ticketNumber} has been deleted successfully."
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Delete ticket error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete ticket. Please try again.'
            ], 500);
        }
    }

    /**
     * Fetch latest tickets for dashboard.
     */
    public function fetchDashboardTickets()
    {
        $tickets = Ticket::with(['category', 'department'])
            ->where('user_id', auth()->id())
            ->orderByRaw('GREATEST(created_at, IFNULL(updated_at, created_at)) DESC')
            ->take(5)
            ->get();

        return response()->json($tickets);
    }

    /**
     * ⭐ Submit Feedback untuk Ticket
     */
    public function storeFeedback(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'required|string|max:1000'
            ]);

            $ticket = Ticket::findOrFail($id);

            if ($ticket->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to add feedback to this ticket.'
                ], 403);
            }

            if (!in_array($ticket->status, ['closed', 'resolved'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only add feedback to closed or resolved tickets.'
                ], 400);
            }

            if ($ticket->feedback) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already submitted feedback for this ticket.'
                ], 400);
            }

            $feedback = TicketFeedback::create([
                'ticket_id' => $id,
                'user_id' => auth()->id(),
                'rating' => $validated['rating'],
                'comment' => $validated['comment']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your feedback!',
                'feedback' => $feedback
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Submit feedback error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit feedback. Please try again.'
            ], 500);
        }
    }

    /**
     * 👁️ Get Feedback dari Ticket
     */
    public function getFeedback($id)
    {
        try {
            $ticket = Ticket::with('feedback')->findOrFail($id);

            if ($ticket->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to view this feedback.'
                ], 403);
            }

            if (!$ticket->feedback) {
                return response()->json([
                    'success' => false,
                    'message' => 'No feedback found for this ticket.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'ticket_id' => $ticket->ticket_id,
                'feedback' => [
                    'rating' => $ticket->feedback->rating,
                    'comment' => $ticket->feedback->comment,
                    'created_at' => $ticket->feedback->created_at->timezone('Asia/Jakarta')->format('d M Y H:i')
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Get feedback error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve feedback.'
            ], 500);
        }
    }

    /**
     * ✅ Store a new ticket (AJAX) - FIXED
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

            // 1️⃣ DETERMINA TARGET USER (Support On-Behalf)
            if ($request->has('user_id') && $request->user_id) {
                // Future-proof: If admin/IT creates ticket for created user
                $user = User::find($request->user_id);
                if (!$user) {
                    return response()->json(['success' => false, 'message' => 'User not found'], 404);
                }
            } else {
                $user = Auth::user();
            }

            // 2️⃣ GET USER CONTEXT
            $departmentId = $user->department_id ?? Department::first()?->id;
            if (!$departmentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No department found. Please assign a department to this user.',
                ], 400);
            }

            // 3️⃣ REGIONAL LOGIC
            $regionId = null;
            $assignedItId = null;

            if ($user->location_id) {
                $location = \App\Models\Location::find($user->location_id);
                if ($location && $location->region_id) {
                    $regionId = $location->region_id;
                    
                    // Auto-assign to IT in this region (Optional)
                    $assignedIt = User::whereIn('role', ['tim it', 'it'])
                        ->where('region_id', $regionId)
                        ->inRandomOrder()
                        ->first();
                        
                    if ($assignedIt) {
                        $assignedItId = $assignedIt->id;
                    }
                }
            }

            // ✅ ANTI-DUPLICATE CHECK
            // Prevent double submit (Same User + Same Category + Same Description + < 1 min)
            $isDuplicate = Ticket::where('user_id', $user->id)
                ->where('category_id', $validated['category_id'])
                ->where('description', $validated['description'])
                ->where('created_at', '>=', now()->subMinutes(1))
                ->exists();

            if ($isDuplicate) {
                 return response()->json([
                    'success' => false,
                    'message' => 'Duplicate ticket detected. Please wait a moment before resubmitting.',
                ], 429);
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
                'region_id'     => $regionId, // 📌 SAVED TO TICKET
                'assigned_to'   => $assignedItId,
            ]);

            // Generate unique ticket_id using helper
            $ticket->ticket_id = \App\Helpers\TicketHelper::generateTicketId();
            $ticket->timestamps = false; // Disable timestamp update for ticket_id
            $ticket->save();
            $ticket->timestamps = true;

            // Load relations
            $ticket->load(['category', 'user.department', 'department']);
            $ticket->refresh();

            // 4️⃣ EMAIL NOTIFICATION — kirim ke akun Tim IT di regional yang sama
            try {
                $targetRegionId = $ticket->region_id;

                if (!$targetRegionId) {
                    Log::warning("Ticket {$ticket->ticket_id}: Tidak ada region_id — notifikasi email dilewati. " .
                        "User location_id=" . ($user->location_id ?? 'null'));
                } else {
                    // Ambil semua email Tim IT yang berada di regional yang sama
                    $itEmails = User::whereIn('role', ['tim it', 'it'])
                        ->where('region_id', $targetRegionId)
                        ->whereNotNull('email')
                        ->where('email', '!=', '')
                        ->pluck('email')
                        ->toArray();

                    if (empty($itEmails)) {
                        Log::warning("Ticket {$ticket->ticket_id}: Tidak ada Tim IT dengan email di region_id={$targetRegionId}. " .
                            "Pastikan akun Tim IT memiliki email yang terisi.");
                    } else {
                        Mail::to($itEmails)->send(new TicketCreatedMail($ticket));
                        Log::info("Ticket {$ticket->ticket_id} notifikasi terkirim ke " . count($itEmails) . " Tim IT: " . implode(', ', $itEmails));
                    }
                }

            } catch (\Exception $e) {
                Log::warning('Email ticket gagal dikirim', ['error' => $e->getMessage()]);
            }


            // ✅ RESPONSE menggunakan accessor dari Model
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
                    
                    // ✅ Menggunakan accessor dari Model (auto NULL-safe)
                    'created_at' => $ticket->created_at_formatted,
                    'created_at_formatted' => $ticket->created_at_formatted,
                    'updated_at' => $ticket->updated_at ? $ticket->updated_at->timezone('Asia/Jakarta')->format('d M Y, H:i') : null,
                    'updated_at_formatted' => $ticket->updated_at_formatted,
                    'resolved_at' => $ticket->resolved_at ? $ticket->resolved_at->timezone('Asia/Jakarta')->format('d M Y, H:i') : null,
                    'resolved_at_formatted' => $ticket->resolved_at_formatted,
                    'response_date' => $ticket->response_date,
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
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket, please try again later.',
                'error_detail' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * ✅ Display ticket details (AJAX) - FIXED
     */
    public function show($id)
    {
        try {
            $ticket = Ticket::with(['category', 'user.department', 'department'])
                ->where('user_id', Auth::id())
                ->findOrFail($id);

            // Handle attachments (Auto-casted by Model)
            // $ticket->attachments is already an array or null

            // ✅ RESPONSE menggunakan accessor dari Model
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
                    
                    'resolution_notes' => $ticket->resolution_notes,
                    
                    // ✅ Add Transfer Info
                    'assigned_to' => $ticket->assigned_to,
                    'region' => $ticket->region ? $ticket->region->name : null,
                    'transfer_logs' => $ticket->transferLogs->map(function($log) {
                        return [
                            'from' => $log->fromRegion ? $log->fromRegion->name : 'N/A',
                            'to' => $log->toRegion ? $log->toRegion->name : 'N/A',
                            'by' => $log->transferredBy ? $log->transferredBy->name : 'Unknown',
                            'note' => $log->note, // Added note
                            'date' => $log->created_at->format('d M Y H:i')
                        ];
                    }),

                    // ✅ Menggunakan accessor dari Model (auto NULL-safe)
                    'created_at' => $ticket->created_at_formatted,
                    'created_at_formatted' => $ticket->created_at_formatted,
                    'updated_at' => $ticket->updated_at ? $ticket->updated_at->timezone('Asia/Jakarta')->format('d M Y H:i') : null,
                    'updated_at_formatted' => $ticket->updated_at_formatted,
                    'resolved_at' => $ticket->resolved_at ? $ticket->resolved_at->timezone('Asia/Jakarta')->format('d M Y H:i') : null,
                    'resolved_at_formatted' => $ticket->resolved_at_formatted,
                    'response_date' => $ticket->response_date,
                    'response_at_formatted' => $ticket->updated_at_formatted,
                ],
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Show ticket error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'ticket_id' => $id,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve ticket details.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}