<?php

namespace App\Http\Controllers;

use App\Models\Diagnosis;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DiagnosisController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
        ]);

        // Check if diagnosis already exists globally or for this user
        $exists = Diagnosis::where('code', $request->code)
            ->where(function($q) use ($request) {
                $q->whereNull('user_id')
                  ->orWhere('user_id', $request->user()->id);
            })->exists();

        if ($exists) {
            return response()->json(['message' => 'Diagnosis code already exists'], 422);
        }

        $diagnosis = Diagnosis::create([
            'code' => $request->code,
            'name' => $request->name,
            'category' => $request->category,
            'user_id' => $request->user()->id,
        ]);

        return response()->json($diagnosis);
    }
}
