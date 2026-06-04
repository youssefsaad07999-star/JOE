<?php

use App\Models\ProductModels\Product;
use App\Models\ProductModels\ProductVariant;
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
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ProductVariant::class)->constrained()->cascadeOnDelete();
            $table->timestamps();

            // $table->id();

            // // 1. Mandatory: Every image MUST belong to a main product
            // $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete();

            // // 2. Nullable: This image can be a general product image, OR tied to a specific variant color/size
            // $table->foreignIdFor(ProductVariant::class)->nullable()->constrained()->cascadeOnDelete();

            // // 3. The Missing Link: Stores the path to the file (e.g., 'products/images/tshirt-black.jpg')
            // $table->string('image_path');

            // // 4. Highly Recommended: Allows you to order images (e.g., Thumbnail / Main Image = 0, Gallery = 1, 2)
            // $table->integer('sort_order')->default(0);

            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
