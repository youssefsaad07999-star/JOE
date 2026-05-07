<?php

use App\Models\ProductModels\Brand;
use App\Models\ProductModels\Color;
use App\Models\ProductModels\Fit;
use App\Models\ProductModels\Product;
use App\Models\ProductModels\Size;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Brand::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Size::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Fit::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Color::class)->constrained()->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->integer('stock_quantity')->default(0);
            $table->decimal('price_override', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
