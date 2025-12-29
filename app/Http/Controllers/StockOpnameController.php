<?php

namespace App\Http\Controllers;

use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\DoctorInventory;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index()
    {
        $opnames = StockOpname::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(10);
        return view('inventory.stock-opname.index', compact('opnames'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $opname = StockOpname::create([
                'user_id' => Auth::id(),
                'status' => 'draft',
                'notes' => $request->notes,
                'started_at' => now(),
            ]);

            $items = DoctorInventory::where('user_id', Auth::id())->get();
            foreach($items as $item) {
                StockOpnameItem::create([
                    'stock_opname_id' => $opname->id,
                    'doctor_inventory_id' => $item->id,
                    'system_qty' => $item->stock_qty,
                    'actual_qty' => $item->stock_qty,
                    'difference' => 0,
                ]);
            }
            
            DB::commit();
            return redirect()->route('stock-opnames.show', $opname);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to start stock opname: ' . $e->getMessage());
        }
    }

    public function show(StockOpname $stockOpname)
    {
        if ($stockOpname->user_id !== Auth::id()) abort(403);
        
        $stockOpname->load(['items.doctorInventory']);
        return view('inventory.stock-opname.show', compact('stockOpname'));
    }

    public function updateItem(Request $request, StockOpname $stockOpname, StockOpnameItem $item)
    {
        if ($stockOpname->user_id !== Auth::id()) abort(403);
        if ($item->stock_opname_id !== $stockOpname->id) abort(404);
        
        $request->validate([
            'actual_qty' => 'required|numeric',
            'notes' => 'nullable|string'
        ]);

        $item->actual_qty = $request->actual_qty;
        $item->difference = $item->actual_qty - $item->system_qty;
        if($request->has('notes')) $item->notes = $request->notes;
        $item->save();

        return response()->json([
            'success' => true,
            'difference' => $item->difference
        ]);
    }

    public function complete(Request $request, StockOpname $stockOpname)
    {
        if ($stockOpname->user_id !== Auth::id()) abort(403);
        if ($stockOpname->status !== 'draft') abort(400, 'Already completed');

        DB::beginTransaction();
        try {
            $stockOpname->status = 'completed';
            $stockOpname->completed_at = now();
            $stockOpname->save();

            // Process adjustments
            foreach($stockOpname->items as $item) {
                if ($item->difference != 0) {
                    $inventory = $item->doctorInventory;
                    $inventory->stock_qty = $item->actual_qty;
                    $inventory->save();

                    if ($inventory->stock_qty <= $inventory->alert_threshold) {
                        $inventory->user->notify(new \App\Notifications\LowStockAlert($inventory));
                    }

                    InventoryTransaction::create([
                        'doctor_inventory_id' => $inventory->id,
                        'type' => 'ADJUSTMENT',
                        'quantity_change' => $item->difference,
                        'notes' => "Stock Opname #" . $stockOpname->id . ($item->notes ? ": " . $item->notes : ""),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('stock-opnames.index')->with('success', 'Stock Opname completed and inventory updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error completing stock opname: ' . $e->getMessage());
        }
    }
    
    public function export(StockOpname $stockOpname)
    {
        if ($stockOpname->user_id !== Auth::id()) abort(403);
        $stockOpname->load(['items.doctorInventory']);
        return view('inventory.stock-opname.export', compact('stockOpname'));
    }
}
