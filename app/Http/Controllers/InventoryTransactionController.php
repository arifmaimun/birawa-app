<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryTransactionController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $type = $request->input('type');
        $search = $request->input('search');

        $transactions = InventoryTransaction::whereHas('inventory', function ($q) use ($search) {
            $q->where('user_id', Auth::id());
            if ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            }
        })
            ->when($startDate, function ($q) use ($startDate) {
                return $q->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($q) use ($endDate) {
                return $q->whereDate('created_at', '<=', $endDate);
            })
            ->when($type, function ($q) use ($type) {
                return $q->where('type', $type);
            })
            ->with('inventory')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('inventory.transactions.index', compact('transactions'));
    }
}
