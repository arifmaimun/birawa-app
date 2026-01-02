<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendshipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $friends = $user->friends;
        
        // Get pending requests received by the user
        $pendingRequests = Friendship::where('friend_id', $user->id)
            ->where('status', 'pending')
            ->with('user') // The sender
            ->get();

        // Get pending requests sent by the user
        $sentRequests = Friendship::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with('friend') // The receiver
            ->get();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'friends' => $friends,
                'pending_received' => $pendingRequests,
                'sent_requests' => $sentRequests,
            ]);
        }

        return view('react_spa');
    }

    /**
     * Send a friend request.
     */
    public function sendRequest(Request $request)
    {
        $request->validate([
            'friend_id' => 'required|exists:users,id|different:user_id',
        ]);

        $user = Auth::user();
        $friendId = $request->friend_id;

        // Check if friendship already exists
        $existing = Friendship::where(function($q) use ($user, $friendId) {
            $q->where('user_id', $user->id)->where('friend_id', $friendId);
        })->orWhere(function($q) use ($user, $friendId) {
            $q->where('user_id', $friendId)->where('friend_id', $user->id);
        })->first();

        if ($existing) {
            if ($existing->status === 'accepted') {
                return response()->json(['message' => 'Already friends'], 409);
            }
            if ($existing->status === 'pending') {
                 return response()->json(['message' => 'Friend request already pending'], 409);
            }
            if ($existing->status === 'blocked') {
                return response()->json(['message' => 'Unable to send request'], 403);
            }
        }

        $friendship = Friendship::create([
            'user_id' => $user->id,
            'friend_id' => $friendId,
            'status' => 'pending',
        ]);

        return response()->json($friendship, 201);
    }

    /**
     * Update friendship status (Accept/Block).
     */
    public function acceptRequest(Request $request, Friendship $friendship)
    {
        $user = Auth::user();

        // Only the recipient can accept
        if ($friendship->friend_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $friendship->update(['status' => 'accepted']);

        // Create reverse record for bidirectional friendship
        Friendship::firstOrCreate([
            'user_id' => $friendship->friend_id,
            'friend_id' => $friendship->user_id,
        ], [
            'status' => 'accepted'
        ]);

        return response()->json($friendship);
    }

    public function destroy(Friendship $friendship)
    {
        $user = Auth::user();
        
        if ($friendship->user_id !== $user->id && $friendship->friend_id !== $user->id) {
            abort(403);
        }

        // Remove the reverse relationship if it exists
        Friendship::where('user_id', $friendship->friend_id)
            ->where('friend_id', $friendship->user_id)
            ->delete();

        $friendship->delete();

        return response()->json(['message' => 'Friendship removed']);
    }
}
