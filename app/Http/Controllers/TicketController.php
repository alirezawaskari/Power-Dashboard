<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\{Ticket, EventLog};
use App\Enums\{EventType, TicketStatus, TicketPriority, ThreadMode};
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class TicketController extends Controller
{
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $req)
    {
        $q = Ticket::query();
        
        // Filter by status
        if ($status = $req->query('status')) {
            $q->where('status', $status);
        }
        
        // Filter by assignee
        if ($assigneeId = $req->query('assignee_id')) {
            $q->where('assignee_id', $assigneeId);
        }
        
        // Filter by device
        if ($deviceId = $req->query('device_id')) {
            $q->where('device_id', $deviceId);
        }
        
        // Filter by creator
        if ($creatorId = $req->query('creator_id')) {
            $q->where('user_id', $creatorId);
        }

        $tickets = $q->with(['creator:id,name', 'assignee:id,name', 'device:id,name'])
            ->orderByDesc('last_activity_at')
            ->paginate(20);
            
        return response()->json($tickets);
    }

    public function show(Request $req, string $id)
    {
        $ticket = Ticket::with(['creator:id,name', 'assignee:id,name', 'device:id,name'])
            ->findOrFail($id);
            
        return response()->json($ticket);
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'device_id' => 'required|exists:devices,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:' . implode(',', array_column(TicketPriority::cases(), 'value')),
            'assignee_id' => 'nullable|exists:users,id',
        ]);

        return DB::transaction(function () use ($data) {
            $ticket = Ticket::create([
                'user_id' => auth()->id(),
                'device_id' => $data['device_id'],
                'assignee_id' => $data['assignee_id'],
                'status' => TicketStatus::Open,
                'priority' => $data['priority'],
                'thread_mode' => ThreadMode::SnapshotJson,
                'snapshot_json' => [
                    'messages' => [
                        [
                            'id' => 1,
                            'user_id' => auth()->id(),
                            'user_name' => auth()->user()->name,
                            'message' => $data['description'],
                            'timestamp' => now()->toISOString(),
                        ]
                    ],
                    'version' => 1,
                ],
                'last_activity_at' => now(),
            ]);

            // Log ticket creation
            EventLog::create([
                'type' => EventType::TicketCreated,
                'actor_type' => 'user',
                'actor_id' => auth()->id(),
                'subject_type' => 'ticket',
                'subject_id' => $ticket->id,
                'message' => 'Ticket created',
                'context' => [
                    'ticket_id' => $ticket->id,
                    'device_id' => $data['device_id'],
                    'priority' => $data['priority'],
                ],
                'occurred_at' => now(),
            ]);

            // Send notification if assigned
            if ($data['assignee_id']) {
                $this->notificationService->notifyTicketAssigned($ticket);
            }

            return response()->json($ticket->load(['creator:id,name', 'assignee:id,name', 'device:id,name']), 201);
        });
    }

    public function update(Request $req, string $id)
    {
        $ticket = Ticket::findOrFail($id);
        
        $data = $req->validate([
            'status' => 'sometimes|in:' . implode(',', array_column(TicketStatus::cases(), 'value')),
            'priority' => 'sometimes|in:' . implode(',', array_column(TicketPriority::cases(), 'value')),
            'assignee_id' => 'sometimes|nullable|exists:users,id',
        ]);

        $oldAssigneeId = $ticket->assignee_id;
        
        $ticket->update($data);
        $ticket->touchActivity();

        // Log ticket update
        EventLog::create([
            'type' => EventType::TicketUpdated,
            'actor_type' => 'user',
            'actor_id' => auth()->id(),
            'subject_type' => 'ticket',
            'subject_id' => $ticket->id,
            'message' => 'Ticket updated',
            'context' => [
                'ticket_id' => $ticket->id,
                'changes' => $data,
            ],
            'occurred_at' => now(),
        ]);

        // Send notifications for assignment changes
        if (isset($data['assignee_id']) && $data['assignee_id'] !== $oldAssigneeId) {
            if ($data['assignee_id']) {
                $this->notificationService->notifyTicketAssigned($ticket);
            }
        }

        return response()->json($ticket->load(['creator:id,name', 'assignee:id,name', 'device:id,name']));
    }

    public function sendMessage(Request $req, string $id)
    {
        $ticket = Ticket::findOrFail($id);
        
        $data = $req->validate([
            'message' => 'required|string|max:1000',
        ]);

        return DB::transaction(function () use ($ticket, $data) {
            $currentSnapshot = $ticket->snapshot_json ?? ['messages' => [], 'version' => 0];
            $newMessageId = count($currentSnapshot['messages']) + 1;
            
            $currentSnapshot['messages'][] = [
                'id' => $newMessageId,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
                'message' => $data['message'],
                'timestamp' => now()->toISOString(),
            ];
            $currentSnapshot['version']++;

            $ticket->update([
                'snapshot_json' => $currentSnapshot,
                'last_activity_at' => now(),
            ]);

            // Send live chat message via RabbitMQ
            $this->notificationService->sendChatMessage($ticket, auth()->user(), $data['message']);

            return response()->json(['message' => 'Message sent successfully']);
        });
    }
}
