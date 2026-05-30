<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'sku', 'barcode', 'category_id', 'selling_price', 'cost_price',
        'profit_percentage', 'tax_amount', 'include_tax', 'threshold', 'image',
        'promo_price', 'promo_start', 'promo_end', 'primary_supplier_id',
        'unit', 'track_stock',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function primarySupplier()
    {
        return $this->belongsTo(Supplier::class, 'primary_supplier_id');
    }

    public function recipe(): HasOne
    {
        return $this->hasOne(Recipe::class);
    }

    public function isRawMaterial(): bool
    {
        return is_null($this->recipe);
    }

    /**
     * Check if this product can be sold given a quantity.
     * Returns null if ok, or an error string if not.
     */
    public function checkSellable(int $qty, int $storeId): ?string
    {
        $recipe = $this->recipe;
        $hasRecipe = $recipe && $recipe->items->isNotEmpty();

        // Always check recipe ingredient stock if product has a recipe
        if ($hasRecipe) {
            foreach ($recipe->items as $item) {
                $raw = static::find($item->product_id);
                if ($raw) {
                    $rawStock = $raw->getStockForStore($storeId);
                    if ($rawStock < ($item->quantity * $qty)) {
                        return "Stok bahan {$raw->name} tidak mencukupi (tersedia: {$rawStock}, butuh: ".($item->quantity * $qty).')';
                    }
                }
            }
        }

        // Check product stock only if track_stock is enabled
        if ($this->track_stock) {
            $stock = $this->getStockForStore($storeId);
            if ($stock < $qty) {
                return "Stok {$this->name} tidak mencukupi (tersedia: {$stock})";
            }
        } elseif (! $hasRecipe) {
            // track_stock = false AND no recipe = misconfigured product, block it
            return "{$this->name} tidak memiliki resep. Hubungi admin.";
        }

        return null;
    }

    public function stocks()
    {
        return $this->hasMany(StockProduct::class);
    }

    public function getStockForStore($storeId): int
    {
        $stock = $this->stocks()->where('store_id', $storeId)->first();

        return $stock ? $stock->quantity : 0;
    }

    public function getStockTotal(): int
    {
        return $this->stocks()->sum('quantity');
    }

    public function getCurrentPrice(): int
    {
        if ($this->isPromoActive()) {
            return $this->promo_price;
        }

        return $this->selling_price;
    }

    public function isPromoActive(): bool
    {
        if (! $this->promo_price || $this->promo_price <= 0) {
            return false;
        }

        $today = now()->toDateString();

        if ($this->promo_start && $this->promo_end) {
            return $today >= $this->promo_start && $today <= $this->promo_end;
        }

        if ($this->promo_start && ! $this->promo_end) {
            return $today >= $this->promo_start;
        }

        if (! $this->promo_start && $this->promo_end) {
            return $today <= $this->promo_end;
        }

        return false;
    }

    public function getDiscountPercentage(): int
    {
        if (! $this->isPromoActive() || $this->selling_price <= 0) {
            return 0;
        }

        return round((($this->selling_price - $this->promo_price) / $this->selling_price) * 100);
    }
}
