<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Opname #{{ $stockOpname->id }}</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        h1 { margin-bottom: 5px; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; }
        .meta { display: flex; justify-content: space-between; font-size: 14px; color: #555; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #888; }
        @media print {
            button { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <button onclick="window.print()" style="float: right; padding: 5px 10px; cursor: pointer;">Print PDF</button>
        <h1>Laporan Stok Opname</h1>
        <div class="meta">
            <div>
                <strong>ID:</strong> #{{ $stockOpname->id }}<br>
                <strong>Oleh:</strong> {{ $stockOpname->user->name }}<br>
                <strong>Status:</strong> {{ ucfirst($stockOpname->status) }}
            </div>
            <div style="text-align: right;">
                <strong>Mulai:</strong> {{ $stockOpname->started_at->format('d M Y H:i') }}<br>
                <strong>Selesai:</strong> {{ $stockOpname->completed_at ? $stockOpname->completed_at->format('d M Y H:i') : '-' }}<br>
            </div>
        </div>
        @if($stockOpname->notes)
            <p><strong>Catatan:</strong> {{ $stockOpname->notes }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Item / SKU</th>
                <th>Satuan</th>
                <th>Stok Sistem</th>
                <th>Stok Fisik</th>
                <th>Selisih</th>
                <th>Catatan Item</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stockOpname->items as $index => $item)
                @php
                    $style = $item->difference != 0 
                        ? 'font-weight: bold; color: ' . ($item->difference > 0 ? 'green' : 'red') . ';' 
                        : 'color: #aaa;';
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        {{ $item->doctorInventory->item_name }}<br>
                        <small style="color: #666;">{{ $item->doctorInventory->sku }}</small>
                    </td>
                    <td>{{ $item->doctorInventory->unit }}</td>
                    <td>{{ $item->system_qty + 0 }}</td>
                    <td>{{ $item->actual_qty + 0 }}</td>
                    <td style="{{ $style }}">
                        {{ ($item->difference > 0 ? '+' : '') . ($item->difference + 0) }}
                    </td>
                    <td>{{ $item->notes }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ now()->format('d F Y H:i') }}
    </div>
</body>
</html>
