<?php

namespace App\Http\Controllers\Manual\Inventory;

use App\Http\Controllers\Controller;
use App\Models\DoctorInventory;
use App\Models\Product;
use App\Models\StorageLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorInventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = DoctorInventory::query()
            ->with(['product', 'storageLocation'])
            ->where('user_id', Auth::id());

        if ($search = $request->input('search')) {
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $inventories = $query->latest()->paginate(10);

        return view('manual.inventory.stock.index', compact('inventories'));
    }

    public function create()
    {
        $products = Product::where('type', 'goods')->orderBy('name')->get();
        $locations = StorageLocation::where('user_id', Auth::id())->orderBy('name')->get();
        
        return view('manual.inventory.stock.create', compact('products', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'storage_location_id' => 'required|exists:storage_locations,id',
            'stock_qty' => 'required|numeric|min:0',
            'min_stock_level' => 'nullable|numeric|min:0',
        ]);

        // Check if exists
        $exists = DoctorInventory::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->where('storage_location_id', $request->storage_location_id)
            ->first();

        if ($exists) {
            return back()->withErrors(['product_id' => 'This product already exists in the selected location.']);
        }

        $product = Product::find($request->product_id);

        DoctorInventory::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'storage_location_id' => $request->storage_location_id,
            'item_name' => $product->name, // Denormalized name
            'sku' => $product->sku, // Denormalized sku
            'stock_qty' => $request->stock_qty,
            'unit' => 'pcs', // Default unit
            'min_stock_level' => $request->min_stock_level ?? 0,
            'selling_price' => $product->price,
        ]);

        return redirect()->route('manual.inventory.index')
            ->with('success', 'Inventory item added successfully.');
    }

    public function edit(DoctorInventory $inventory)
    {
        // Ensure ownership
        if ($inventory->user_id !== Auth::id()) {
            abort(403);
        }

        $locations = StorageLocation::where('user_id', Auth::id())->orderBy('name')->get();
        
        return view('manual.inventory.stock.edit', compact('inventory', 'locations'));
    }

    public function update(Request $request, DoctorInventory $inventory)
    {
        if ($inventory->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'storage_location_id' => 'required|exists:storage_locations,id',
            'stock_qty' => 'required|numeric|min:0',
            'min_stock_level' => 'nullable|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
        ]);

        $inventory->update([
            'storage_location_id' => $request->storage_location_id,
            'stock_qty' => $request->stock_qty,
            'min_stock_level' => $request->min_stock_level,
            'selling_price' => $request->selling_price,
        ]);

        return redirect()->route('manual.inventory.index')
            ->with('success', 'Inventory updated successfully.');
    }

    public function destroy(DoctorInventory $inventory)
    {
        if ($inventory->user_id !== Auth::id()) {
            abort(403);
        }

        $inventory->delete();

        return redirect()->route('manual.inventory.index')
            ->with('success', 'Inventory item removed successfully.');
    }
}
