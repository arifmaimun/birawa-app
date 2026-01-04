<?php

namespace App\Console\Commands;

use App\Models\DoctorInventory;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ConsolidateInventoryProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:consolidate-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Link existing DoctorInventory items to Products, creating Products if necessary.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = DoctorInventory::whereNull('product_id');
        $count = $query->count();

        $this->info("Found {$count} inventory items without linked Product.");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $query->chunk(100, function ($inventories) use ($bar) {
            foreach ($inventories as $inventory) {
                $product = null;

                // 1. Try match by SKU
                if (! empty($inventory->sku)) {
                    $product = Product::where('sku', $inventory->sku)->first();
                }

                // 2. Try match by Name (if no product found yet)
                if (! $product) {
                    $product = Product::where('name', $inventory->item_name)->first();
                }

                // 3. Create if not found
                if (! $product) {
                    $sku = $inventory->sku;
                    if (empty($sku)) {
                        // Generate a unique SKU if missing
                        do {
                            $sku = 'PRD-'.strtoupper(Str::slug($inventory->item_name)).'-'.Str::random(4);
                        } while (Product::where('sku', $sku)->exists());
                    }

                    $product = Product::create([
                        'sku' => $sku,
                        'name' => $inventory->item_name,
                        'category' => $inventory->category ?? 'Medicine',
                        'type' => 'goods', // Default to goods
                        'cost' => $inventory->average_cost_price ?? 0,
                        'price' => $inventory->selling_price ?? 0,
                        'stock' => 0, // Central stock is separate
                    ]);
                }

                // 4. Link it
                $inventory->product_id = $product->id;
                $inventory->save();

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info('Consolidation complete.');
    }
}
