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
        'unit',
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
