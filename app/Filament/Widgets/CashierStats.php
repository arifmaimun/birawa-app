<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class CashierStats extends BaseWidget
{
    protected function getStats(): array
    {
        $today = now()->startOfDay();
        $userId = Auth::id();
        
        $totalSales = Invoice::where('user_id', $userId)
            ->where('created_at', '>=', $today)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');
            
        $invoiceCount = Invoice::where('user_id', $userId)
            ->where('created_at', '>=', $today)
            ->where('status', '!=', 'cancelled')
            ->count();

        // Calculate payments received today for invoices created by this user
        // Note: Payments might be made today for older invoices, but for daily reconciliation, usually we count payments received *today*.
        // Assuming InvoicePayment has user_id? No, it belongs to Invoice.
        // We can check payments collected *by this user* if we had user_id on payment.
        // But here we check payments on invoices created by this user.
        // Or better: Payments received *today* regardless of invoice date?
        // Usually Cashier wants "How much money is in my drawer?"
        // So simple query on InvoicePayment created_at >= today.
        // But we don't know *who* received the payment if multiple cashiers exist, unless we link Payment to User.
        // For now, assuming single cashier or filtering by invoices created by user.
        
        $cashReceived = InvoicePayment::where('created_at', '>=', $today)
            ->where('method', 'cash')
            ->sum('amount');
            
        $digitalReceived = InvoicePayment::where('created_at', '>=', $today)
            ->whereIn('method', ['transfer', 'qris', 'card'])
            ->sum('amount');

        return [
            Stat::make('Total Sales Today', 'Rp ' . number_format($totalSales, 0, ',', '.'))
                ->description($invoiceCount . ' invoices issued')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Cash In Hand (Today)', 'Rp ' . number_format($cashReceived, 0, ',', '.'))
                ->description('Total Cash Received')
                ->color('warning'),
            Stat::make('Digital Payments (Today)', 'Rp ' . number_format($digitalReceived, 0, ',', '.'))
                ->description('QRIS / Transfer / Card')
                ->color('info'),
        ];
    }
}
