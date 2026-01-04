<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $friends = $user->friends; // Uses the getFriendsAttribute accessor

        // Get recent chats
        // We want users who we have exchanged messages with, ordered by most recent message
        $recentChatUserIds = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($message) use ($user) {
                return $message->sender_id == $user->id ? $message->receiver_id : $message->sender_id;
            })
            ->unique()
            ->values();

        $recentChats = User::whereIn('id', $recentChatUserIds)->get()
            ->sortBy(function ($u) use ($recentChatUserIds) {
                return array_search($u->id, $recentChatUserIds->toArray());
            });

        $activeChatUser = null;
        $messages = collect();

        if ($request->has('user_id')) {
            $activeChatUser = User::findOrFail($request->user_id);

            // Mark messages as read
            Message::where('sender_id', $activeChatUser->id)
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $messages = Message::where(function ($q) use ($user, $activeChatUser) {
                $q->where('sender_id', $user->id)
                    ->where('receiver_id', $activeChatUser->id);
            })->orWhere(function ($q) use ($user, $activeChatUser) {
                $q->where('sender_id', $activeChatUser->id)
                    ->where('receiver_id', $user->id);
            })
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return view('chat.index', compact('friends', 'recentChats', 'activeChatUser', 'messages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $user = Auth::user();

        if ($user->id == $request->receiver_id) {
            return response()->json(['message' => 'Cannot send message to yourself'], 422);
        }

        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return response()->json($message, 201);
    }

    public function getMessages(User $user)
    {
        $currentUser = Auth::user();

        // Mark messages as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $currentUser->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::where(function ($q) use ($currentUser, $user) {
            $q->where('sender_id', $currentUser->id)
                ->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($currentUser, $user) {
            $q->where('sender_id', $user->id)
                ->where('receiver_id', $currentUser->id);
        })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }
}
