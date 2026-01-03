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

        return view('friends.index', compact('friends', 'pendingRequests', 'sentRequests'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $user = Auth::user();
        
        if (empty($query)) {
            $results = [];
        } else {
            // Find users who are NOT me, and NOT already my friend (roughly)
            // Ideally we show status button (Add Friend, Pending, Accepted)
            $results = User::where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->where('id', '!=', $user->id)
                ->limit(20)
                ->get();
                
            // Check friendship status for each result
            $results->each(function($result) use ($user) {
                $friendship = Friendship::where(function($q) use ($user, $result) {
                    $q->where('user_id', $user->id)->where('friend_id', $result->id);
                })->orWhere(function($q) use ($user, $result) {
                    $q->where('user_id', $result->id)->where('friend_id', $user->id);
                })->first();
                
                $result->friendship_status = $friendship ? $friendship->status : null;
                $result->is_sender = $friendship && $friendship->user_id === $user->id;
            });
        }
        
        return view('friends.search', compact('results', 'query'));
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
                $msg = 'Already friends';
            } elseif ($existing->status === 'pending') {
                $msg = 'Friend request already pending';
            } else {
                $msg = 'Unable to send request';
            }
            
            if ($request->wantsJson()) {
                return response()->json(['message' => $msg], 409);
            }
            return back()->with('error', $msg);
        }

        $friendship = Friendship::create([
            'user_id' => $user->id,
            'friend_id' => $friendId,
            'status' => 'pending',
        ]);

        if ($request->wantsJson()) {
            return response()->json($friendship, 201);
        }
        return back()->with('success', 'Friend request sent!');
    }

    /**
     * Update friendship status (Accept/Block).
     */
    public function acceptRequest(Request $request, Friendship $friendship)
    {
        $user = Auth::user();

        // Only the recipient can accept
        if ($friendship->friend_id !== $user->id) {
             if ($request->wantsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
             }
             abort(403);
        }

        $friendship->update(['status' => 'accepted']);

        // Create reverse record for bidirectional friendship logic if needed
        // But our schema has unique constraints and we usually treat one record as bidirectional
        // However, standard implementation often uses one record. 
        // My User model logic `getFriendsAttribute` handles looking at both columns.
        // But `FriendshipController` original code created a reverse record.
        // Let's stick to the original logic if it was intended to have 2 records, 
        // BUT `2026_01_01_161154_create_friendships_table.php` has:
        // $table->unique(['user_id', 'friend_id']);
        // It does NOT have unique(['friend_id', 'user_id']). 
        // Wait, `unique(['user_id', 'friend_id'])` only prevents duplicate A->B.
        // It allows B->A.
        // So yes, we can have 2 records.
        
        Friendship::firstOrCreate([
            'user_id' => $friendship->friend_id,
            'friend_id' => $friendship->user_id,
        ], [
            'status' => 'accepted'
        ]);

        if ($request->wantsJson()) {
            return response()->json($friendship);
        }
        return back()->with('success', 'Friend request accepted!');
    }

    public function destroy(Request $request, Friendship $friendship)
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

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Friendship removed']);
        }
        return back()->with('success', 'Friendship removed');
    }
}
