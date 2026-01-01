<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExpiringBatchesWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $count = \App\Models\DoctorInventoryBatch::where('expiry_date', '<=', now()->addDays(30))->count();

        return [
            Stat::make('Expiring Batches (30 Days)', $count)
                ->description('Batches nearing expiry')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color($count > 0 ? 'danger' : 'success'),
        ];
    }
}
