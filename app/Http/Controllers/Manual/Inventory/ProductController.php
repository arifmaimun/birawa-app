<?php

namespace App\Http\Controllers\Manual\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\DoctorInventory;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        $products = $query->latest()->paginate(10);

        return view('manual.inventory.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Product::distinct()->pluck('category')->filter();
        return view('manual.inventory.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|in:goods,service',
            'cost' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'nullable|string|max:50',
            'sku_mode' => 'required|in:auto,manual',
        ];

        if ($request->input('sku_mode') === 'manual') {
            $rules['sku'] = 'required|string|max:50|unique:products';
        }

        $validated = $request->validate($rules);

        $data = $request->except(['sku_mode']);

        if ($request->input('sku_mode') === 'auto') {
            $data['sku'] = $this->generateSku($request->category, $request->type);
        }

        Product::create($data);

        return redirect()->route('manual.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Product::distinct()->pluck('category')->filter();
        return view('manual.inventory.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|in:goods,service',
            'cost' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'nullable|string|max:50',
            'sku' => ['required', 'string', 'max:50', Rule::unique('products')->ignore($product->id)],
        ];

        $validated = $request->validate($rules);

        $product->update($validated);

        return redirect()->route('manual.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Prevent deletion if used in invoices or doctor inventory
        if (InvoiceItem::where('product_id', $product->id)->exists()) {
            return back()->with('error', 'Cannot delete product linked to invoices.');
        }

        if (DoctorInventory::where('product_id', $product->id)->exists()) {
             return back()->with('error', 'Cannot delete product existing in doctor inventory.');
        }

        $product->delete();

        return redirect()->route('manual.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    private function generateSku($category, $type)
    {
        // Generate prefix: 3 letters of category (or PRD/SVC)
        $prefix = $category ? strtoupper(substr($category, 0, 3)) : ($type == 'goods' ? 'PRD' : 'SVC');
        $prefix = preg_replace('/[^A-Z]/', '', $prefix); // Keep only letters
        if (strlen($prefix) < 3) $prefix = str_pad($prefix, 3, 'X');

        // Find last product with this prefix to determine sequence
        $lastProduct = Product::where('sku', 'like', $prefix . '-%')
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastProduct) {
            $parts = explode('-', $lastProduct->sku);
            $lastSeq = end($parts);
            if (is_numeric($lastSeq)) {
                $sequence = intval($lastSeq) + 1;
            }
        }

        return $prefix . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
