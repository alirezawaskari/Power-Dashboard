<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

final class NotificationController extends Controller
{
    public function index(Request $req)
    {
        $q = Notification::query()->where('user_id', auth()->id());
        
        // Filter by type
        if ($type = $req->query('type')) {
            $q->where('type', $type);
        }
        
        // Filter by status
        if ($status = $req->query('status')) {
            $q->where('status', $status);
        }
        
        // Filter by channel
        if ($channel = $req->query('channel')) {
            $q->where('channel', $channel);
        }

        $notifications = $q->orderByDesc('created_at')
            ->paginate(20);
            
        return response()->json($notifications);
    }

    public function unread(Request $req)
    {
        $notifications = Notification::query()
            ->where('user_id', auth()->id())
            ->whereNull('read_at')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();
            
        return response()->json($notifications);
    }

    public function markAsRead(Request $req, string $id)
    {
        $notification = Notification::where('user_id', auth()->id())
            ->findOrFail($id);
            
        $notification->markAsRead();
        
        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllAsRead(Request $req)
    {
        Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
            
        return response()->json(['message' => 'All notifications marked as read']);
    }
}
