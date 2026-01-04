<?php

namespace App\Http\Controllers\Manual\Finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::query()
            ->with(['patient.client'])
            ->where('user_id', Auth::id());

        if ($search = $request->input('search')) {
            $query->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('patient', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhereHas('client', function($sq) use ($search) {
                            $sq->where('name', 'like', "%{$search}%");
                        });
                  });
        }

        $invoices = $query->latest()->paginate(10);

        return view('manual.finance.invoices.index', compact('invoices'));
    }

    public function create(Request $request)
    {
        $patients = Patient::with('client')->orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        
        $visit = null;
        if ($request->has('visit_id')) {
            $visit = \App\Models\Visit::find($request->visit_id);
        }

        return view('manual.finance.invoices.create', compact('patients', 'products', 'visit'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'visit_id' => 'nullable|exists:visits,id',
            'due_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(uniqid()); // Simple generator
            // Better generator: INV-YYYYMMDD-XXXX
            $lastInvoice = Invoice::latest()->first();
            $sequence = 1;
            if ($lastInvoice && preg_match('/INV-\d{8}-(\d+)/', $lastInvoice->invoice_number, $matches)) {
                $sequence = intval($matches[1]) + 1;
            }
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['quantity'] * $item['price'];
            }

            $invoice = Invoice::create([
                'user_id' => Auth::id(), // Doctor/Admin creating it
                'patient_id' => $request->patient_id,
                'visit_id' => $request->visit_id,
                'invoice_number' => $invoiceNumber,
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'total_amount' => $subtotal,
                'deposit_amount' => 0,
                'remaining_balance' => $subtotal,
                'payment_status' => 'unpaid',
                'due_date' => $request->due_date,
                'status' => 'draft',
            ]);

            foreach ($request->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'description' => Product::find($item['product_id'])->name,
                ]);
            }

            DB::commit();

            return redirect()->route('manual.invoices.show', $invoice)
                ->with('success', 'Invoice created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating invoice: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['items.product', 'patient.client']);
        return view('manual.finance.invoices.show', compact('invoice'));
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->user_id !== Auth::id()) {
            abort(403);
        }

        if ($invoice->payment_status === 'paid') {
             return back()->with('error', 'Cannot delete a paid invoice.');
        }

        // Delete items first (though cascade might handle it, better to be explicit or safe)
        $invoice->items()->delete();
        $invoice->delete();

        return redirect()->route('manual.invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }
}
