<?php

namespace App\Http\Controllers\Manual\Clinical;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query()->where('user_id', Auth::id());

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $clients = $query->latest()->paginate(10);

        return view('manual.clinical.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('manual.clinical.clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'is_business' => 'boolean',
            'business_name' => 'nullable|string|max:255',
        ]);

        Client::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'is_business' => $request->is_business ?? false,
            'business_name' => $request->business_name,
        ]);

        return redirect()->route('manual.clients.index')
            ->with('success', 'Client registered successfully.');
    }

    public function edit(Client $client)
    {
        if ($client->user_id !== Auth::id()) {
            abort(403);
        }

        return view('manual.clinical.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        if ($client->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'is_business' => 'boolean',
            'business_name' => 'nullable|string|max:255',
        ]);

        $client->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'is_business' => $request->is_business ?? false,
            'business_name' => $request->business_name,
        ]);

        return redirect()->route('manual.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        if ($client->user_id !== Auth::id()) {
            abort(403);
        }

        if ($client->patients()->count() > 0) {
            return back()->with('error', 'Cannot delete client with registered patients.');
        }

        $client->delete();

        return redirect()->route('manual.clients.index')
            ->with('success', 'Client removed successfully.');
    }
}
