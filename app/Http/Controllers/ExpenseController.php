<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::where('user_id', Auth::id())
            ->orderByDesc('transaction_date')
            ->orderByDesc('created_at')
            ->paginate(15);
            
        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:OPEX,CAPEX',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        Expense::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'category' => $request->category,
            'amount' => $request->amount,
            'transaction_date' => $request->transaction_date,
            'notes' => $request->notes,
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully.');
    }

    public function show(Expense $expense)
    {
        if ($expense->user_id !== Auth::id()) {
            abort(403);
        }
        return view('expenses.show', compact('expense'));
    }

    public function destroy(Expense $expense)
    {
        if ($expense->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Prevent deletion if linked to inventory transaction?
        // Ideally yes, but for MVP let's allow it or just warn.
        // Actually, if it's linked to InventoryTransaction, we should probably cascade delete or block.
        // The foreign key `related_expense_id` is on InventoryTransaction, nullable.
        // So deleting expense won't break SQL integrity if we didn't set cascade there.
        // But conceptually it's messy.
        
        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted.');
    }
}
