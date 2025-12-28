<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'visit_id', 
        'invoice_number', 
        'total_amount', 
        'deposit_amount',
        'remaining_balance',
        'payment_status', // unpaid, partial, paid
        'due_date',
        'access_token'
    ];

    protected $casts = [
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
    ];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function recalculateStatus()
    {
        $paid = $this->payments()->sum('amount');
        $this->remaining_balance = max(0, $this->total_amount - $this->deposit_amount - $paid);

        if ($this->remaining_balance <= 0) {
            $this->payment_status = 'paid';
        } elseif ($paid > 0 || $this->deposit_amount > 0) {
            $this->payment_status = 'partial';
        } else {
            $this->payment_status = 'unpaid';
        }
        
        $this->save();
    }
}
