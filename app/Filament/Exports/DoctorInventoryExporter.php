<?php

namespace App\Filament\Exports;

use App\Models\DoctorInventory;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class DoctorInventoryExporter extends Exporter
{
    protected static ?string $model = DoctorInventory::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('user_id'),
            ExportColumn::make('item_name'),
            ExportColumn::make('sku')
                ->label('SKU'),
            ExportColumn::make('stock_qty'),
            ExportColumn::make('unit'),
            ExportColumn::make('alert_threshold'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('base_unit'),
            ExportColumn::make('purchase_unit'),
            ExportColumn::make('conversion_ratio'),
            ExportColumn::make('average_cost_price'),
            ExportColumn::make('selling_price'),
            ExportColumn::make('category'),
            ExportColumn::make('storage_location_id'),
            ExportColumn::make('product_id'),
            ExportColumn::make('is_sold'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your doctor inventory export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
