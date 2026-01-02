<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\Visit;
use App\Services\InventoryService;
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
            $invoice->update([
                'access_token' => (string) Str::uuid(),
                'token_expires_at' => now()->addHours(48)
            ]);
        }
        
        $invoice->load('payments');

        return view('invoices.show', compact('invoice'));
    }

    public function showPublic($token)
    {
        $invoice = Invoice::where('access_token', $token)->with(['visit.patient.owners', 'invoiceItems', 'payments'])->firstOrFail();

        // LOGIC: If token_expires_at is past (or fallback to created_at if null) AND user is NOT logged in
        $expiresAt = $invoice->token_expires_at ?? $invoice->created_at->addHours(48);
        
        if ($expiresAt->isPast() && !Auth::check()) {
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

            // 3. Add Medical Usage Items (Medications/Consumables AND Services)
            // Load medical records and their usage logs
            $visit->load(['medicalRecords.usageLogs.doctorInventory', 'medicalRecords.usageLogs.service']);

            foreach ($visit->medicalRecords as $record) {
                foreach ($record->usageLogs as $log) {
                    if ($log->doctorInventory) {
                        $inventory = $log->doctorInventory;

                        // Skip if not for sale
                        if (!$inventory->is_sold) {
                            continue;
                        }

                        $qty = $log->quantity_used;
                        $price = $inventory->selling_price > 0 ? $inventory->selling_price : ($inventory->average_cost_price * 1.2); // Default 20% margin if no selling price
                        $subtotal = $qty * $price;

                        InvoiceItem::create([
                            'invoice_id' => $invoice->id,
                            'description' => $inventory->item_name,
                            'quantity' => $qty, 
                            'unit_price' => $price,
                            'unit_cost' => $inventory->average_cost_price,
                            'product_id' => $inventory->product_id, 
                            'doctor_inventory_id' => null, // Stock already deducted in Medical Record
                        ]);
                        
                        $total += $subtotal;
                    } elseif ($log->service) {
                        $service = $log->service;
                        $qty = $log->quantity_used;
                        $price = $service->price;
                        $subtotal = $qty * $price;

                        InvoiceItem::create([
                            'invoice_id' => $invoice->id,
                            'description' => $service->service_name,
                            'quantity' => $qty,
                            'unit_price' => $price,
                            'unit_cost' => 0,
                            'product_id' => null,
                        ]);

                        $total += $subtotal;
                    }
                }
            }

            // Update Total
            $invoice->update([
                'total_amount' => $total,
                'remaining_balance' => $total,
            ]);
        });

        // Redirect to show newly created invoice
        $invoice = Invoice::where('visit_id', $visit->id)->first();
        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice generated successfully.');
    }

    public function update(Request $request, Invoice $invoice)
    {
        $ownerId = $invoice->visit ? $invoice->visit->user_id : $invoice->user_id;
        if ($ownerId !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'deposit_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $invoice->update([
            'deposit_amount' => $request->deposit_amount ?? 0,
            'notes' => $request->notes,
            'due_date' => $request->due_date,
        ]);
        
        $invoice->recalculateStatus();

        return back()->with('success', 'Invoice details updated.');
    }

    public function storePayment(Request $request, Invoice $invoice, InventoryService $inventoryService)
    {
        $ownerId = $invoice->visit ? $invoice->visit->user_id : $invoice->user_id;
        if ($ownerId !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|string',
            'notes' => 'nullable|string',
            'paid_at' => 'nullable|date',
        ]);

        DB::transaction(function () use ($request, $invoice, $inventoryService) {
            InvoicePayment::create([
                'invoice_id' => $invoice->id,
                'amount' => $request->amount,
                'method' => $request->method,
                'notes' => $request->notes,
                'paid_at' => $request->paid_at ?? now(),
            ]);

            $invoice->recalculateStatus();

            // Commit stock if paid
            if ($invoice->payment_status === 'paid' && !$invoice->stock_committed) {
                foreach ($invoice->invoiceItems as $item) {
                    if ($item->doctor_inventory_id) {
                        try {
                            $inventoryService->commitStock($item->doctor_inventory_id, $item->quantity);
                        } catch (\Exception $e) {
                            // Log error but don't fail payment? Or fail payment?
                            // Better to fail so we know something is wrong with stock
                            throw $e;
                        }
                    }
                }
                $invoice->update(['stock_committed' => true, 'status' => 'issued']);
            }
        });

        return back()->with('success', 'Payment recorded.');
    }

    public function cancel(Invoice $invoice, InventoryService $inventoryService)
    {
        $ownerId = $invoice->visit ? $invoice->visit->user_id : $invoice->user_id;
        if ($ownerId !== Auth::id()) {
            abort(403);
        }

        if ($invoice->status === 'cancelled') {
             return back()->with('error', 'Invoice already cancelled.');
        }

        DB::transaction(function () use ($invoice, $inventoryService) {
             $invoice->update(['status' => 'cancelled']);
             
             // Release reservation if not committed
             if (!$invoice->stock_committed) {
                 foreach ($invoice->invoiceItems as $item) {
                    if ($item->doctor_inventory_id) {
                        $inventoryService->releaseStock($item->doctor_inventory_id, $item->quantity);
                    }
                }
             }
        });

        return back()->with('success', 'Invoice cancelled.');
    }

    public function destroyPayment(Invoice $invoice, InvoicePayment $payment)
    {
        $ownerId = $invoice->visit ? $invoice->visit->user_id : $invoice->user_id;
        if ($ownerId !== Auth::id()) {
            abort(403);
        }
        
        if ($payment->invoice_id !== $invoice->id) {
            abort(404);
        }

        $payment->delete();
        $invoice->recalculateStatus();

        return back()->with('success', 'Payment removed.');
    }
}
