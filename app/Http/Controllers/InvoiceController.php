<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::whereHas('visit', function ($q) {
            $q->where('user_id', Auth::id());
        });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('visit.patient', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('visit.patient.owners', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->with(['visit.patient.owners', 'visit.patient'])->latest()->paginate(10);

        return view('invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        if ($invoice->visit->user_id !== Auth::id()) {
            abort(403);
        }

        // Auto-generate access token if missing (for backward compatibility)
        if (empty($invoice->access_token)) {
            $invoice->update(['access_token' => (string) Str::uuid()]);
        }

        return view('invoices.show', compact('invoice'));
    }

    public function showPublic($token)
    {
        $invoice = Invoice::where('access_token', $token)->with(['visit.patient.owners', 'invoiceItems'])->firstOrFail();

        // LOGIC: If created_at > 48 hours AND user is NOT logged in -> Redirect to login
        // If user IS logged in, they can view it regardless of time.
        if ($invoice->created_at->addHours(48)->isPast() && !Auth::check()) {
             return redirect()->route('login')->with('error', 'This invoice link has expired. Please log in to view.');
        }

        return view('invoices.public_show', compact('invoice'));
    }

    public function createFromVisit(Visit $visit)
    {
        if ($visit->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if invoice exists
        $existingInvoice = Invoice::where('visit_id', $visit->id)->first();
        if ($existingInvoice) {
            return redirect()->route('invoices.show', $existingInvoice);
        }

        DB::transaction(function () use ($visit) {
            // 1. Create Invoice Header
            $invoice = Invoice::create([
                'visit_id' => $visit->id,
                'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                'total_amount' => 0, // Will update later
                'payment_status' => 'unpaid',
                'access_token' => (string) Str::uuid(),
            ]);

            $total = 0;

            // 2. Add Transport Fee
            if ($visit->transport_fee > 0) {
                $amount = $visit->transport_fee;
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => 'Transport Fee (' . ($visit->distance_km ?? 0) . ' km)',
                    'quantity' => 1,
                    'unit_price' => $amount,
                    'unit_cost' => 0,
                    // 'product_id' => null
                ]);
                $total += $amount;
            }

            // 3. Add Medical Usage Items (Medications/Consumables)
            // Load medical records and their usage logs
            $visit->load('medicalRecords.usageLogs.doctorInventory');

            foreach ($visit->medicalRecords as $record) {
                foreach ($record->usageLogs as $log) {
                    $inventory = $log->doctorInventory;
                    if ($inventory) {
                        $qty = $log->quantity_used;
                        $price = $inventory->selling_price > 0 ? $inventory->selling_price : ($inventory->average_cost_price * 1.2); // Default 20% margin if no selling price
                        $subtotal = $qty * $price;

                        InvoiceItem::create([
                            'invoice_id' => $invoice->id,
                            'description' => $inventory->item_name,
                            'quantity' => $qty, 
                            'unit_price' => $price,
                            'unit_cost' => $inventory->average_cost_price,
                            'product_id' => null, 
                        ]);
                        
                        $total += $subtotal;
                    }
                }
            }

            // Update Total
            $invoice->update(['total_amount' => $total]);
        });

        // Redirect to show newly created invoice
        $invoice = Invoice::where('visit_id', $visit->id)->first();
        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice generated successfully.');
    }

    public function markPaid(Invoice $invoice)
    {
        if ($invoice->visit->user_id !== Auth::id()) {
            abort(403);
        }

        $invoice->update(['payment_status' => 'paid']);

        return redirect()->back()->with('success', 'Invoice marked as paid.');
    }
}
