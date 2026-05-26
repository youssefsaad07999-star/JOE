<?php

namespace App\Models;

use App\Models\ProductModels\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(ProductVariant::class)
            ->withPivot('quantity', 'unit_price', 'subtotal')
            ->withTimestamps();

    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
