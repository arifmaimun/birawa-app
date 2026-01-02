<?php

return [
    'low_stock' => [
        'subject' => 'Low Stock Alert: :item_name',
        'line1' => 'The stock for item ":item_name" has dropped below the threshold.',
        'line2' => 'Current Stock: :stock_qty :unit',
        'line3' => 'Threshold: :threshold',
        'action' => 'Restock Now',
        'line4' => 'Please restock as soon as possible.',
        'title' => 'Low Stock Alert',
        'message' => 'Item :item_name is low on stock (:stock_qty :unit).',
    ],
];
