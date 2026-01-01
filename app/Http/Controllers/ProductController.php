<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::where('type', 'goods')->latest()->paginate(10);
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Product::distinct()->pluck('category')->filter();
        return view('products.create', compact('categories'));
    }

    public function checkSku(Request $request)
    {
        $exists = Product::where('sku', $request->sku)->exists();
        return response()->json(['exists' => $exists]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|in:goods,service',
            'cost' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'nullable|string|max:50',
        ];

        if ($request->input('sku_mode') !== 'auto') {
            $rules['sku'] = 'required|string|max:50|unique:products';
        }

        $request->validate($rules);

        $data = $request->all();

        if ($request->input('sku_mode') === 'auto') {
            $data['sku'] = $this->generateSku($request->category, $request->type);
        }

        Product::create($data);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
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

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->id,
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:50',
            'type' => 'required|in:barang,jasa',
            'cost' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
