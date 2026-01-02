<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Visit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DailyProfitStats extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $user = Auth::user();
        if (!$user) return [];

        $today = Carbon::today();

        // 1. Revenue (Invoices created today)
        // We filter by invoices linked to visits of this doctor
        $revenue = Invoice::whereHas('visit', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->whereDate('created_at', $today)
            ->sum('total_amount');

        // 2. COGS - Medicine
        // Sum of (unit_cost * quantity)
        $medicineCost = InvoiceItem::whereHas('invoice', function ($q) use ($user, $today) {
                $q->whereDate('created_at', $today)
                  ->whereHas('visit', function ($vq) use ($user) {
                      $vq->where('user_id', $user->id);
                  });
            })
            ->selectRaw('sum(unit_cost * quantity) as total_cost')
            ->value('total_cost') ?? 0;

        // 3. COGS - Transport (Fuel)
        // Visits scheduled for today
        $visitsToday = Visit::where('user_id', $user->id)
            ->whereDate('scheduled_at', $today)
            ->get();
            
        $totalDistance = $visitsToday->sum('distance_km');
        $fuelRate = 2000; // Est. 2000 IDR per km
        $transportCost = $totalDistance * $fuelRate;

        $totalCOGS = $medicineCost + $transportCost;
        $netProfit = $revenue - $totalCOGS;

        return [
            Stat::make('Revenue Today', 'Rp ' . number_format($revenue, 0, ',', '.'))
                ->description('Total Invoice')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
                
            Stat::make('Est. COGS Today', 'Rp ' . number_format($totalCOGS, 0, ',', '.'))
                ->description('Obat + Bensin (Rp 2000/km)')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
                
            Stat::make('Net Profit Today', 'Rp ' . number_format($netProfit, 0, ',', '.'))
                ->description('Revenue - COGS')
                ->color($netProfit >= 0 ? 'success' : 'danger'),
        ];
    }
}
