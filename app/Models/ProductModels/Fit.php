<?php

namespace App\Models\ProductModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fit extends Model
{
    use HasFactory;

    protected $gaurded;

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
