<?php

namespace App\Http\Controllers\It;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Location;
use App\Models\Department;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketCreatedMail;
use Illuminate\Support\Facades\Log;
use App\Mail\TicketResolvedMail;
use App\Mail\TicketTransferredNotification;
use App\Mail\TicketClosedNotification;

class TicketController extends Controller
{
    // ============================================================
    // 📋 INDEX — Daftar tiket aktif
    // ============================================================
    public function index(Request $request)
    {
        $currentUser = Auth::user(); // ✅ Get current user first
        $categories = Category::all();
        $locations = Location::all(); 

        // Fetch users: Only show users in the IT Staff's Region
        // If IT Staff has NO Region, they see NO users (strict regional coverage)
        $usersQuery = User::whereIn('role', ['user', 'staff']);

        if ($currentUser->region_id) {
            $usersQuery->whereHas('location', function($q) use ($currentUser) {
                $q->where('region_id', $currentUser->region_id);
            });
            $users = $usersQuery->orderBy('name')->get();
        } else {
            // User has no region assigned -> Cannot select users for new ticket
            $users = collect([]); 
        }

        $tickets = Ticket::with(['category', 'user', 'department', 'assignedTo', 'transferLogs']) // Load assignedTo & logs
            ->whereNotIn('status', ['closed'])
            // ✅ FILTER: Logika "Regional Assignment"
            // 1. Show tickets explicitly assigned to this IT staff.
            // 2. Fallback: Show tickets in the IT staff's REGION if assigned_to is null (safety net).
            ->where(function ($query) use ($currentUser) {
                $query->where('assigned_to', $currentUser->id);
                
                // Optional: If you want IT to see ALL tickets in their region regardless of assignment:
                // $query->orWhere('region_id', $currentUser->region_id);
                
                // OR: Only if unassigned:
                if ($currentUser->region_id) {
                     $query->orWhere(function ($subQuery) use ($currentUser) {
                        $subQuery->whereNull('assigned_to')
                                 ->where('region_id', $currentUser->region_id);
                    });
                }
            })
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(fn($q2) => $q2
                    ->where('description', 'like', "%{$search}%")
                    ->orWhere('ticket_id', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                );
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('it.index-ticket', compact('categories', 'tickets', 'users', 'locations'));
    }

    // ============================================================
    // ➕ STORE — Buat tiket baru (bisa atas nama user)
    // ============================================================
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id'       => 'required|exists:users,id',
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

            // Get selected user
            $targetUser = User::findOrFail($validated['user_id']);

            // Determine department (use user's department or fallback)
            $departmentId = $targetUser->department_id ?? Department::first()?->id;
            
            if (!$departmentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User does not have a department assigned.',
                ], 400);
            }

            // ✅ REGIONAL ASSIGNMENT LOGIC
            $regionId = null;
            $assignedItId = null;

            if ($targetUser->location_id) {
                $location = \App\Models\Location::find($targetUser->location_id);
                if ($location && $location->region_id) {
                    $regionId = $location->region_id;
                }
            }

            // Create ticket
            $ticket = Ticket::create([
                'user_id'       => $targetUser->id,
                'created_by'    => Auth::id(), // Optional: track who created it
                'department_id' => $departmentId,
                'category_id'   => $validated['category_id'],
                'priority'      => strtolower($validated['priority']),
                'description'   => $validated['description'],
                'attachments'   => json_encode($filePaths),
                'status'        => 'waiting',
                'region_id'     => $regionId,
                'assigned_to'   => null,
            ]);

            // Generate unique ticket_id using helper
            $ticket->ticket_id = \App\Helpers\TicketHelper::generateTicketId();
            $ticket->timestamps = false;
            $ticket->save();
            $ticket->timestamps = true;

            // Send Email Notification
            // Send Email Notification to ALL IT in Region
            try {
                $recipients = [];

                // 1. Get All IT Staff in Region
                if ($regionId) {
                    $itEmails = User::whereIn('role', ['tim it', 'it'])
                        ->where('region_id', $regionId)
                        ->whereNotNull('email')
                        ->pluck('email')
                        ->toArray();
                    $recipients = array_merge($recipients, $itEmails);
                }

                // 2. Recipients are already all IT in Region
                
                $recipients = array_unique($recipients);

                if (!empty($recipients)) {
                    // Optimized: Send single email with multiple recipients
                    Mail::to($recipients)->send(new TicketCreatedMail($ticket));
                }
            } catch (\Exception $e) {
                Log::warning('Email ticket gagal dikirim', ['error' => $e->getMessage()]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ticket created successfully for ' . $targetUser->name,
                'ticket' => $ticket
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed!',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create ticket', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket.',
            ], 500);
        }
    }

    // ============================================================
    // 🎫 SHOW — Detail tiket (dipakai di modal AJAX)
    // ✅ FIXED: NULL-safe untuk updated_at dan resolved_at
    // ============================================================
    public function show(Ticket $ticket)
    {
        // Muat semua relasi yang dibutuhkan
        $ticket->load(['user', 'category', 'department']);

        // Logika untuk memastikan attachments selalu berupa array
        $attachments = $ticket->attachments;
        if (is_string($attachments)) {
            $decoded = json_decode($attachments, true);
            $attachments = is_array($decoded) ? $decoded : [];
        } elseif (!is_array($attachments)) {
            $attachments = [];
        }

        return response()->json([
            'id' => $ticket->id,
            'ticket_id' => $ticket->ticket_id,
            'description' => $ticket->description,
            'priority' => $ticket->priority,
            'status' => $ticket->status,
            'assigned_to' => $ticket->assigned_to, 
            'attachments' => $attachments,
            'resolution_notes' => $ticket->resolution_notes ?? '',
            'transfer_logs' => $ticket->transferLogs->map(function($log) {
                return [
                    'from' => $log->fromRegion ? $log->fromRegion->name : 'N/A',
                    'to' => $log->toRegion ? $log->toRegion->name : 'N/A',
                    'by' => $log->transferredBy ? $log->transferredBy->name : 'Unknown',
                    'note' => $log->note, // Added note
                    'date' => $log->created_at->format('d M Y H:i')
                ];
            }),
            
            // ✅ DATES - NULL-safe menggunakan accessor dari Model
            'report_date' => $ticket->created_at_formatted,
            'created_at_formatted' => $ticket->created_at_formatted,
            
            // ✅ Response Date = updated_at (bisa NULL untuk waiting)
            'response_date' => $ticket->updated_at_formatted,
            'response_at_formatted' => $ticket->updated_at_formatted,
            
            // ✅ Resolved Date = resolved_at (bisa NULL)
            'resolved_date' => $ticket->resolved_at 
                ? $ticket->resolved_at->format('d M Y H:i')
                : null,
            'resolved_at_formatted' => $ticket->resolved_at_formatted,
            
            // Relations
            'user' => [
                'name' => optional($ticket->user)->name ?? 'User Deleted',
                'location' => optional($ticket->user->location)->name ?? 'Unknown Location',
            ],
            'category' => [
                'name' => optional($ticket->category)->name ?? 'No Category',
            ],
            'department' => [
                'name' => optional($ticket->department)->name ?? 'No Department',
            ],
        ]);
    }

    // ============================================================
    // 🧭 UPDATE (umum)
    // ============================================================
    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status'   => 'nullable|in:waiting,in_progress,pending,resolved,closed',
            'priority' => 'nullable|in:low,medium,high,urgent,critical',
        ]);
        
        $ticket->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ticket updated successfully.',
                'ticket'  => $ticket
            ]);
        }

        return back()->with('success', 'Ticket updated successfully.');
    }

    // ============================================================
    // ⚙️ UPDATE FIELD (via AJAX)
    // ✅ FIXED: NULL-safe untuk response
    // ============================================================
    public function updateField(Request $request, Ticket $ticket)
    {
        $validator = Validator::make($request->all(), [
            'field' => 'required|in:status,priority',
            'value' => 'required|string',
            'resolution_notes' => 'nullable|string|max:2000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $field = $validated['field'];
        $value = $validated['value'];

        if ($field === 'status') {
            $ticket->status = $value;
            $resolutionNotes = $request->resolution_notes;

            // Simpan resolution_notes untuk pending, resolved, dan closed
            if (in_array($value, ['pending', 'resolved', 'closed']) && $resolutionNotes) {
                $ticket->resolution_notes = $resolutionNotes;
            }
            
            // Set resolved_at timestamp if status is resolved
            if ($value === 'resolved' && !$ticket->resolved_at) {
                $ticket->resolved_at = now();
            }
        } else {
            // For priority updates
            $ticket->{$field} = $value;
        }

        // ✅ AUTO-ASSIGN CLAIM LOGIC:
        // Jika tiket belum ada yang menangani (unassigned), 
        // maka otomatis ditugaskan ke staff IT yang melakukan update ini.
        if (!$ticket->assigned_to) {
            $ticket->assigned_to = Auth::id();
        }

        $ticket->save();

        if ($field === 'status' && $value === 'closed') {
                Log::info('-------- TICKET CLOSED EMAIL DEBUG START --------');
                Log::info('Ticket ID: ' . $ticket->id);
                try {
                    $ticket->load('user'); // Ensure user is loaded
                    
                    if (!$ticket->user) {
                        Log::error('❌ FAILED: Ticket creator (User) not found or deleted.');
                    } elseif (empty($ticket->user->email)) {
                        Log::error('❌ FAILED: User found but EMAIL is empty. User ID: ' . $ticket->user->id . ', Name: ' . $ticket->user->name);
                    } else {
                        Log::info('✅ User Found: ID=' . $ticket->user->id . ', Name=' . $ticket->user->name . ', Email=' . $ticket->user->email);
                        
                        Mail::to($ticket->user->email)->send(new TicketClosedNotification(
                            $ticket, 
                            Auth::user()->name,
                            $ticket->resolution_notes
                        ));
                        
                        Log::info('✅ Email queued successfully to: ' . $ticket->user->email);
                    }
                } catch (\Exception $e) {
                    Log::error('❌ EXCEPTION: Failed to send ticket closed email: ' . $e->getMessage());
                    Log::error($e->getTraceAsString());
                }
                Log::info('-------- TICKET CLOSED EMAIL DEBUG END --------');
            } else {
                Log::info('Status update: ' . $value . ' (Not trigger closed email)');
            }
            
            // Handled above in auto-assign logic

        // Muat relasi terbaru dan refresh dari database
        $ticket->load('category', 'user', 'department');
        $ticket->refresh();

        // ✅ Response dengan NULL-safe menggunakan accessor dari Model
        return response()->json([
            'success' => true,
            'message' => ucfirst($field) . ' updated successfully.',
            'ticket' => [
                'id' => $ticket->id,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'response_date' => $ticket->updated_at_formatted,
                'response_at_formatted' => $ticket->updated_at_formatted,
                'resolved_date' => $ticket->resolved_at 
                    ? $ticket->resolved_at->format('d M Y H:i')
                    : null,
                'resolved_at_formatted' => $ticket->resolved_at_formatted,
            ]
        ]);
    }

    // ============================================================
    // 🚚 TRANSFER — Kirim tiket ke staff lain (berdasarkan lokasi)
    // ============================================================
    public function getStaffByLocation(Location $location)
    {
        $staff = $location->users()
            ->whereIn('role', ['it', 'tim it', 'staff']) // Adjust roles as needed
            ->select('id', 'name', 'email')
            ->get();
            
        return response()->json($staff);
    }

    public function transfer(Request $request, Ticket $ticket)
    {
        $request->validate([
            'region_id' => 'required|exists:regions,id', // ✅ Validate Region
            'note' => 'nullable|string'
        ]);
        
        $regionId = $request->region_id;
        $regionName = 'Unknown Region';

        // 1. Find Region Name
        $region = \App\Models\Region::find($regionId);
        if (!$region) {
            return response()->json(['success' => false, 'message' => 'Target region not found.'], 404);
        }

        // ✅ PREVENT SAME REGION TRANSFER
        if ($ticket->region_id == $regionId) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket is already in Region ' . $region->name . '. No transfer needed.'
            ], 400);
        }

        $regionName = $region->name;
        
        // ✅ Log Transfer History
        \App\Models\TicketTransferLog::create([
            'ticket_id' => $ticket->id,
            'from_region_id' => $ticket->region_id, // Old Region
            'to_region_id' => $region->id, // New Region
            'transferred_by' => Auth::id(),
            'note' => $request->note // Save Reason
        ]);

        $ticket->region_id = $region->id; // ✅ Update Region

        // 2. Clear assigned_to (so IT staff in new region can claim it)
        $ticket->assigned_to = null;

        $ticket->save();

        // Send Email to New Region IT Staff
        try {
            $recipients = [];

            // 1. Get All IT Staff in New Region
            $newRegionITs = User::whereIn('role', ['tim it', 'it'])
                ->where('region_id', $regionId)
                ->whereNotNull('email')
                ->pluck('email')
                ->toArray();
            $recipients = array_merge($recipients, $newRegionITs);

            // 2. Recipients are already all IT in Region

            $recipients = array_unique($recipients);

            if (!empty($recipients)) {
                // Get Old Region Data safely
                $oldRegionId = $ticket->transferLogs->last()->from_region_id ?? null;
                $oldRegion = $oldRegionId ? \App\Models\Region::find($oldRegionId) : new \stdClass();
                if (!isset($oldRegion->name)) $oldRegion->name = "Unknown Region";

                Log::info("Sending Transfer Email to: " . implode(',', $recipients));
                
                Mail::to($recipients)->send(new TicketTransferredNotification(
                    $ticket,
                    Auth::user()->name,
                    $oldRegion, // From Region object
                    $region,    // To Region object
                    $request->note
                ));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send transfer email: ' . $e->getMessage());
        }

        $message = "Ticket transferred to Region: {$regionName} (Pending Assignment)";

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    // ============================================================
    // 🗂️ RIWAYAT — Tiket yang sudah closed dengan filter lengkap
    // ============================================================
    public function riwayat(Request $request)
    {
        $categories = Category::all();
        $currentUser = Auth::user();

        $tickets = Ticket::with(['category', 'user', 'department'])
            ->where('status', 'closed')
            // ✅ FILTER LOCATION/ASSIGNED
            ->where(function ($query) use ($currentUser) {
                // IT Staff can see history if:
                // 1. They were assigned the unique ticket
                // 2. OR the ticket user is in their location (and it wasn't exclusively assigned to someone else, though for history usually we want to see all location history)
                // Let's stick to the "Location Visibility" rule for history + Assigned
                $query->where('assigned_to', $currentUser->id)
                      ->orWhereHas('user', function ($q) use ($currentUser) {
                           $q->where('location_id', $currentUser->location_id);
                      });
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function($q2) use ($search) {
                    $q2->where('description', 'like', "%{$search}%")
                       ->orWhere('ticket_id', 'like', "%{$search}%")
                       ->orWhere('id', 'like', "%{$search}%")
                       ->orWhereHas('user', function($q3) use ($search) {
                           $q3->where('name', 'like', "%{$search}%");
                       });
                });
            })
            ->when($request->filled('start_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->start_date);
            })
            ->when($request->filled('end_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->end_date);
            })
            ->when($request->filled('category'), function ($q) use ($request) {
                $q->where('category_id', $request->category);
            })
            ->when($request->filled('priority'), function ($q) use ($request) {
                $q->where('priority', $request->priority);
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderBy('resolved_at', 'desc')
            ->paginate(10)
            ->appends($request->query());

        return view('it.riwayat-ticket', compact('categories', 'tickets'));
    }

    // ============================================================
    // 📊 DASHBOARD IT
    // ============================================================
    public function dashboard()
    {
        $currentUser = Auth::user();

        // ✅ Base Query with Correct Region Filter
        $baseQuery = Ticket::query()->where(function ($query) use ($currentUser) {
            $query->where('assigned_to', $currentUser->id)
                  ->orWhere(function ($subQuery) use ($currentUser) {
                      $subQuery->whereNull('assigned_to')
                               ->where('region_id', $currentUser->region_id);
                  });
        });

        // Clone base query for counts to avoid reusing the same builder instance and its constraints incorrectly if not careful
        $activeTickets    = (clone $baseQuery)->whereIn('status', ['waiting','in_progress'])->count();
        $pendingTickets   = (clone $baseQuery)->where('status', 'pending')->count();
        $completedTickets = (clone $baseQuery)->where('status', 'resolved')->count();
        $urgentTickets    = (clone $baseQuery)->where('priority', 'urgent')->where('status', '!=', 'resolved')->count();

        $recentTickets = Ticket::with(['category', 'user.location', 'department', 'assignedTo'])
            ->where(function ($query) use ($currentUser) {
                // Same logic for recent tickets
                $query->where('assigned_to', $currentUser->id)
                      ->orWhere(function ($subQuery) use ($currentUser) {
                          $subQuery->whereNull('assigned_to')
                                   ->where('region_id', $currentUser->region_id);
                      });
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('it.IT', compact(
            'activeTickets',
            'pendingTickets',
            'completedTickets',
            'urgentTickets',
            'recentTickets'
        ));
    }
}