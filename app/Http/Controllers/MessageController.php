<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Get messages with a specific user.
     */
    public function index(Request $request, $userId)
    {
        $currentUser = Auth::user();

        $messages = Message::where(function ($q) use ($currentUser, $userId) {
            $q->where('sender_id', $currentUser->id)
                ->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($currentUser, $userId) {
            $q->where('sender_id', $userId)
                ->where('receiver_id', $currentUser->id);
        })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    /**
     * Send a message.
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $currentUser = Auth::user();

        if ($currentUser->id == $request->receiver_id) {
            return response()->json(['message' => 'Cannot send message to yourself'], 422);
        }

        $message = Message::create([
            'sender_id' => $currentUser->id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return response()->json($message, 201);
    }

    /**
     * Mark messages as read.
     */
    public function markAsRead(Request $request, Message $message)
    {
        $currentUser = Auth::user();

        if ($message->receiver_id !== $currentUser->id) {
            abort(403);
        }

        $message->update(['read_at' => now()]);

        return response()->json(['message' => 'Message marked as read']);
    }

    public function unreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get recent conversations.
     */
    public function conversations()
    {
        $userId = Auth::id();

        // This query is a bit complex. We want the latest message for each conversation.
        // Simplified approach: get all messages involving the user, group by the other person.

        $conversations = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($message) use ($userId) {
                return $message->sender_id == $userId ? $message->receiver_id : $message->sender_id;
            })
            ->map(function ($messages, $partnerId) {
                $lastMessage = $messages->first(); // Since we ordered by desc
                $partner = User::find($partnerId);
                $unreadCount = $messages->where('receiver_id', Auth::id())->whereNull('read_at')->count();

                return [
                    'partner' => $partner,
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                ];
            })
            ->values(); // Reset keys to array

        return response()->json($conversations);
    }
}
