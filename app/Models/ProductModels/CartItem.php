<?php

namespace App\Models\ProductModels;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'product_variant_id',
        'quantity',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function scopeForUser(Builder $q, int $userId): Builder
    {
        return $q->where('user_id', $userId);
    }

    public function scopeForSession(Builder $q, string $sessionId): Builder
    {
        return $q->where('session_id', $sessionId);
    }

    public function getUnitPriceAttribute(): float
    {
        return $this->variant->price_override
            ? $this->variant->price_override + $this->variant->product->base_price
            : $this->variant->product->base_price;
    }

    public function getLineTotalAttribute(): float
    {
        return $this->unit_price * $this->quantity;
    }
}
