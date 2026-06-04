<?php

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
        Schema::table('product_images', function (Blueprint $table) {
            // 1. Add the missing image path column
            $table->string('image_path')->after('product_id');

            // 2. Add an optional sort order column for layout positioning
            $table->integer('sort_order')->default(0)->after('image_path');

            // 3. Drop the old strict constraint and make it nullable
            // NOTE: We drop the column first, then recreate it as nullable
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn('product_variant_id');
        });

        // Re-adding the variant column as nullable in a fresh macro declaration
        Schema::table('product_images', function (Blueprint $table) {
            $table->foreignIdFor(ProductVariant::class)
                ->nullable()
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            //
        });
    }
};
