<?php

namespace App\Models\ProductModels;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $guarded;

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function globalImages()
    {
        return $this->hasMany(ProductImage::class)
            ->whereNull('color_id')
            ->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasMany(ProductImage::class)
            ->where('is_primary', true);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function fit(): BelongsTo
    {
        return $this->belongsTo(Fit::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function scopeInCategory($query, Category $category)
    {
        return $query->whereIn('category_id', $category->getDescendantIds());
    }

    public function scopeIsActive(Builder $q)
    {
        return $q->where('is_active', true);
    }
}
