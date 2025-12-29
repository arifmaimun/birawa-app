<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransfer;
use App\Models\InventoryTransferItem;
use App\Models\DoctorInventory;
use App\Models\InventoryTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryTransferController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $transfers = InventoryTransfer::with(['requester', 'items', 'approver'])
            ->where(function($q) use ($userId) {
                $q->where('requester_id', $userId)
                  ->orWhere(function($q2) use ($userId) {
                      $q2->where('source_type', 'doctor')->where('source_id', $userId);
                  })
                  ->orWhere(function($q3) use ($userId) {
                      $q3->where('target_type', 'doctor')->where('target_id', $userId);
                  });
            })
            ->latest()
            ->paginate(10);

        return view('inventory-transfers.index', compact('transfers'));
    }

    public function create()
    {
        // Get list of doctors for target/source selection (exclude self)
        $doctors = User::where('id', '!=', Auth::id())->get();
        
        // Get current user's inventory items
        $myInventory = DoctorInventory::where('user_id', Auth::id())->orderBy('item_name')->get();

        return view('inventory-transfers.create', compact('doctors', 'myInventory'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:request_from_central,send_to_doctor',
            'target_doctor_id' => 'required_if:type,send_to_doctor|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function() use ($request) {
            $transfer = new InventoryTransfer();
            $transfer->transfer_number = 'TRF-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            $transfer->requester_id = Auth::id();
            $transfer->status = 'pending';
            
            if ($request->type === 'request_from_central') {
                $transfer->source_type = 'central';
                $transfer->source_id = null;
                $transfer->target_type = 'doctor';
                $transfer->target_id = Auth::id();
            } else {
                $transfer->source_type = 'doctor';
                $transfer->source_id = Auth::id();
                $transfer->target_type = 'doctor';
                $transfer->target_id = $request->target_doctor_id;
            }
            
            $transfer->notes = $request->notes;
            $transfer->save();

            foreach($request->items as $itemData) {
                InventoryTransferItem::create([
                    'inventory_transfer_id' => $transfer->id,
                    'item_name' => $itemData['item_name'],
                    'sku' => $itemData['sku'] ?? null,
                    'quantity_requested' => $itemData['quantity'],
                ]);
            }
        });

        return redirect()->route('inventory-transfers.index')->with('success', 'Transfer request created.');
    }

    public function show(InventoryTransfer $inventoryTransfer)
    {
        $userId = Auth::id();
        
        // Access Check
        $isRequester = $inventoryTransfer->requester_id === $userId;
        $isSource = $inventoryTransfer->source_type === 'doctor' && $inventoryTransfer->source_id === $userId;
        $isTarget = $inventoryTransfer->target_type === 'doctor' && $inventoryTransfer->target_id === $userId;
        $isAdmin = $userId === 1; // Simplistic admin check
        
        if (!$isRequester && !$isSource && !$isTarget && !$isAdmin) {
             abort(403);
        }

        return view('inventory-transfers.show', compact('inventoryTransfer'));
    }
    
    public function approve(Request $request, InventoryTransfer $inventoryTransfer)
    {
        $userId = Auth::id();
        $canApprove = false;
        
        if ($inventoryTransfer->source_type === 'central') {
            // Assume ID 1 is admin.
            if ($userId === 1) $canApprove = true; 
        } elseif ($inventoryTransfer->source_type === 'doctor') {
            if ($inventoryTransfer->source_id === $userId) $canApprove = true;
        }
        
        if (!$canApprove) abort(403, 'Cannot approve this transfer');
        
        DB::transaction(function() use ($inventoryTransfer, $userId) {
             foreach($inventoryTransfer->items as $item) {
                 // 1. Deduct from Source
                 if ($inventoryTransfer->source_type === 'doctor') {
                     $sourceInv = DoctorInventory::where('user_id', $inventoryTransfer->source_id)
                         ->where(function($q) use ($item) {
                             $q->where('sku', $item->sku)->orWhere('item_name', $item->item_name);
                         })->first();
                         
                     if (!$sourceInv || $sourceInv->stock_qty < $item->quantity_requested) {
                         throw new \Exception("Insufficient stock for {$item->item_name}");
                     }
                     
                     $sourceInv->decrement('stock_qty', $item->quantity_requested);
                     
                     if ($sourceInv->stock_qty <= $sourceInv->alert_threshold) {
                         $sourceInv->user->notify(new \App\Notifications\LowStockAlert($sourceInv));
                     }

                     InventoryTransaction::create([
                         'doctor_inventory_id' => $sourceInv->id,
                         'type' => 'OUT',
                         'quantity_change' => -$item->quantity_requested,
                         'notes' => "Transfer #{$inventoryTransfer->transfer_number} to " . ($inventoryTransfer->target_type === 'doctor' ? 'Doctor' : 'Central'),
                     ]);
                 }
                 
                 // 2. Add to Target
                 if ($inventoryTransfer->target_type === 'doctor') {
                     $targetInv = DoctorInventory::firstOrCreate(
                         [
                             'user_id' => $inventoryTransfer->target_id,
                             'sku' => $item->sku
                         ],
                         [
                             'item_name' => $item->item_name,
                             'stock_qty' => 0,
                             'base_unit' => 'unit',
                             'purchase_unit' => 'unit',
                             'conversion_ratio' => 1,
                             'alert_threshold' => 5,
                             'category' => 'General'
                         ]
                     );
                     
                     $targetInv->increment('stock_qty', $item->quantity_requested);
                     
                     InventoryTransaction::create([
                         'doctor_inventory_id' => $targetInv->id,
                         'type' => 'IN',
                         'quantity_change' => $item->quantity_requested,
                         'notes' => "Transfer #{$inventoryTransfer->transfer_number} from " . ($inventoryTransfer->source_type === 'doctor' ? 'Doctor' : 'Central'),
                     ]);
                     
                     $item->update(['quantity_approved' => $item->quantity_requested]);
                 }
             }
             
             $inventoryTransfer->update([
                 'status' => 'approved',
                 'approved_by' => $userId,
                 'approved_at' => now()
             ]);
        });
        
        return back()->with('success', 'Transfer approved and processed.');
    }
    
    public function reject(Request $request, InventoryTransfer $inventoryTransfer)
    {
         $userId = Auth::id();
         // Same approval logic for rejection
        $canApprove = false;
        if ($inventoryTransfer->source_type === 'central') {
            if ($userId === 1) $canApprove = true; 
        } elseif ($inventoryTransfer->source_type === 'doctor') {
            if ($inventoryTransfer->source_id === $userId) $canApprove = true;
        }
        
        if (!$canApprove) abort(403);
        
        $inventoryTransfer->update([
            'status' => 'rejected',
            'approved_by' => $userId,
            'approved_at' => now(),
            'notes' => $inventoryTransfer->notes . "\nRejected by approver."
        ]);
        
        return back()->with('success', 'Transfer rejected.');
    }
}
