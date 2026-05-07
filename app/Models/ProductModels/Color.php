<?php

namespace App\Models\ProductModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Color extends Model
{
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
}
