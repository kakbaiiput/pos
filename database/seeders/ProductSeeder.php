<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockProduct;
use App\Models\Store;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $makanan = Category::firstOrCreate(['name' => 'Makanan']);
        $minuman = Category::firstOrCreate(['name' => 'Minuman']);
        $snack = Category::firstOrCreate(['name' => 'Snack']);

        $suppliers = Supplier::all();
        $supplierMap = [];
        foreach ($suppliers as $s) {
            $supplierMap[$s->name] = $s->id;
        }

        $products = [
            ['name' => 'Nasi Goreng', 'category_id' => $makanan->id, 'selling_price' => 25000, 'cost_price' => 15000, 'stock' => 50, 'threshold' => 10, 'supplier' => 'PT. Sumber Pangan Jaya', 'barcode' => '8901234567890'],
            ['name' => 'Mie Goreng', 'category_id' => $makanan->id, 'selling_price' => 20000, 'cost_price' => 12000, 'stock' => 50, 'threshold' => 10, 'supplier' => 'PT. Sumber Pangan Jaya', 'barcode' => '8901234567891'],
            ['name' => 'Ayam Goreng', 'category_id' => $makanan->id, 'selling_price' => 22000, 'cost_price' => 13000, 'stock' => 30, 'threshold' => 5, 'supplier' => 'CV. Daging Segar Nusantara', 'barcode' => '8901234567892'],
            ['name' => 'Sate Ayam', 'category_id' => $makanan->id, 'selling_price' => 30000, 'cost_price' => 18000, 'stock' => 25, 'threshold' => 5, 'supplier' => 'CV. Daging Segar Nusantara', 'barcode' => '8901234567893'],
            ['name' => 'Bakso', 'category_id' => $makanan->id, 'selling_price' => 18000, 'cost_price' => 10000, 'stock' => 40, 'threshold' => 10, 'supplier' => 'CV. Daging Segar Nusantara', 'barcode' => '8901234567894'],
            ['name' => 'Es Teh', 'category_id' => $minuman->id, 'selling_price' => 5000, 'cost_price' => 2000, 'stock' => 100, 'threshold' => 20, 'supplier' => 'CV. Berkah Minuman Sejahtera', 'barcode' => '8901234567895'],
            ['name' => 'Es Kopi', 'category_id' => $minuman->id, 'selling_price' => 12000, 'cost_price' => 6000, 'stock' => 50, 'threshold' => 10, 'supplier' => 'CV. Berkah Minuman Sejahtera', 'barcode' => '8901234567896'],
            ['name' => 'Kopi Hitam', 'category_id' => $minuman->id, 'selling_price' => 10000, 'cost_price' => 5000, 'stock' => 50, 'threshold' => 10, 'supplier' => 'CV. Berkah Minuman Sejahtera', 'barcode' => '8901234567897'],
            ['name' => 'Teh Hangat', 'category_id' => $minuman->id, 'selling_price' => 6000, 'cost_price' => 2500, 'stock' => 80, 'threshold' => 15, 'supplier' => 'CV. Berkah Minuman Sejahtera', 'barcode' => '8901234567898'],
            ['name' => 'Jus Alpukat', 'category_id' => $minuman->id, 'selling_price' => 15000, 'cost_price' => 8000, 'stock' => 30, 'threshold' => 5, 'supplier' => 'PT. Fresh Produce Indo', 'barcode' => '8901234567899'],
            ['name' => 'Jus Mangga', 'category_id' => $minuman->id, 'selling_price' => 12000, 'cost_price' => 6000, 'stock' => 30, 'threshold' => 5, 'supplier' => 'PT. Fresh Produce Indo', 'barcode' => '8901234567900'],
            ['name' => 'Kerupuk', 'category_id' => $snack->id, 'selling_price' => 3000, 'cost_price' => 1500, 'stock' => 100, 'threshold' => 20, 'supplier' => 'UD. Maju Snack Indonesia', 'barcode' => '8901234567901'],
            ['name' => 'Kentang Goreng', 'category_id' => $snack->id, 'selling_price' => 10000, 'cost_price' => 5000, 'stock' => 40, 'threshold' => 10, 'supplier' => 'UD. Maju Snack Indonesia', 'barcode' => '8901234567902'],
            ['name' => 'Cireng', 'category_id' => $snack->id, 'selling_price' => 8000, 'cost_price' => 4000, 'stock' => 35, 'threshold' => 8, 'supplier' => 'UD. Maju Snack Indonesia', 'barcode' => '8901234567903'],
            ['name' => 'Pisang Goreng', 'category_id' => $snack->id, 'selling_price' => 8000, 'cost_price' => 4000, 'stock' => 30, 'threshold' => 8, 'supplier' => 'PT. Fresh Produce Indo', 'barcode' => '8901234567904'],
        ];

        $stores = Store::where('status', 'active')->get();

        foreach ($products as $p) {
            $supplierId = $supplierMap[$p['supplier']] ?? null;

            $product = Product::firstOrCreate(['name' => $p['name']], [
                'name' => $p['name'],
                'category_id' => $p['category_id'],
                'selling_price' => $p['selling_price'],
                'cost_price' => $p['cost_price'],
                'threshold' => $p['threshold'],
                'sku' => str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT),
                'barcode' => $p['barcode'] ?? null,
                'primary_supplier_id' => $supplierId,
            ]);

            foreach ($stores as $store) {
                StockProduct::firstOrCreate(
                    ['product_id' => $product->id, 'store_id' => $store->id],
                    ['quantity' => $p['stock']]
                );
            }
        }
    }
}
