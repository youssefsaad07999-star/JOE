<?php

use App\Models\ProductModels\Category;
use App\Models\ProductModels\Product;
use App\Models\ProductModels\ProductVariant;
use Database\Seeders\ProductSeeders\CategorySeeder;
use Database\Seeders\ProductSeeders\ColorSeeder;
use Database\Seeders\ProductSeeders\FitSeeder;
use Database\Seeders\ProductSeeders\SizeSeeder;

beforeEach(function () {
    $this->seed([
        CategorySeeder::class,
        FitSeeder::class,
        ColorSeeder::class,
        SizeSeeder::class,
    ]);
});

dataset('genders', ['men', 'women']);

describe('Subcategory page', function () {

    it('renders the subcategory page successfully', function (string $gender) {
        $category = Category::where('depth', 'category')
            ->whereHas('parent', fn ($q) => $q->where('slug', $gender))
            ->with('children')
            ->first();

        $this->get(route("$gender.subcategory.show", [
            'category' => $category,
            'subcategory' => $category->children->first(),
        ]))->assertOk();

    })->with('genders');

    it('returns 404 for unknown subcategory', function (string $gender) {
        $category = Category::where('depth', 'category')
            ->whereHas('parent', fn ($q) => $q->where('slug', $gender))
            ->first();

        $this->get(route("$gender.subcategory.show", [
            'category' => $category,
            'subcategory' => 'non-existent',
        ]))->assertNotFound();

    })->with('genders');

    it('returns 404 when the subcategory does not belong to the category', function (string $gender) {

        $category = Category::where('depth', 'category')
            ->whereHas('parent', fn ($q) => $q->where('slug', $gender))
            ->first();

        $wrongSubcategory = Category::where('depth', 'subcategory')
            ->whereHas('parent', fn ($q) => $q->where('id', '!=', $category->id))
            ->first();

        $this->get(route("$gender.subcategory.show", [
            'category' => $category,
            'subcategory' => $wrongSubcategory,
        ]))->assertNotFound();

    })->with('genders');

    it('return 404 when subcategory belongs to the opposite gender', function (string $gender) {
        $oppositeGender = $gender === 'men' ? 'women' : 'men';

        $oppositeCategory = Category::where('depth', 'category')
            ->whereHas('parent', fn ($q) => $q->where('slug', $oppositeGender))
            ->first();

        $oppositeSubcategory = Category::where('depth', 'subcategory')
            ->where('parent_id', $oppositeCategory->id)
            ->first();

        $this->get(route("$gender.subcategory.show", [
            'category' => $oppositeCategory,
            'subcategory' => $oppositeSubcategory,
        ]))->assertNotFound();

    })->with('genders');

    it('does not show product at the same subcategory for the opposite gender', function (string $gender) {
        $opposite = $gender === 'men' ? 'women' : 'men';

        $category = Category::where('depth', 'category')
            ->whereHas('parent', fn ($q) => $q->where('slug', $gender))
            ->first();

        $subcategory = Category::where('depth', 'subcategory')
            ->where('parent_id', $category->id)
            ->first();

        $oppositeSubcategory = Category::where('depth', 'subcategory')
            ->whereHas('parent.parent', fn ($q) => $q->where('slug', $opposite))
            ->first();

        $product = Product::factory()
            ->for($oppositeSubcategory, 'category')
            ->has(ProductVariant::factory(), 'variants')
            ->create();

        $this->get(route("$gender.subcategory.show", [
            'category' => $category,
            'subcategory' => $subcategory,
        ]))->assertDontSeeText($product->name);
    })->with('genders');

    it('does not show a product from a sibling subcategory', function (string $gender) {
        $category = Category::where('depth', 'category')
            ->whereHas('parent', fn ($q) => $q->where('slug', $gender))
            ->with('children')
            ->first();

        $Subcategory = $category->children->first();

        $siblingSubcategory = $category->children->skip(1)->first();

        $siblingProduct = Product::factory()
            ->for($siblingSubcategory, 'category')
            ->has(ProductVariant::factory(), 'variants')
            ->create();

        $this->get(route("$gender.subcategory.show", [
            $category,
            $Subcategory,
        ]))->assertDontSeeText($siblingProduct->name);
    })->with('genders');

    it('shows each product name that belongs to the subcategory', function (string $gender) {

        $subcategory = Category::where('depth', 'subcategory')
            ->whereHas('parent.parent', fn ($q) => $q->where('slug', $gender))
            ->with('parent')
            ->first();

        $category = $subcategory->parent;

        $products = Product::factory()->count(3)
            ->for($subcategory, 'category')
            ->has(ProductVariant::factory(), 'variants')
            ->create();

        $response = $this->get(route("$gender.subcategory.show", [
            $category,
            $subcategory,
        ]));

        $products->each(
            fn ($product) => $response->assertSeeText($product->name)
        );
    })->with('genders');

    it('shows empty state message if no products exist', function (string $gender) {
        $category = Category::where('depth', 'category')
            ->whereHas('parent', fn ($q) => $q->where('slug', $gender))
            ->with('children')
            ->first();

        $subcategory = $category->children->first();

        $this->get(route("$gender.subcategory.show", [
            $category,
            $subcategory,
        ]))->assertSeeText('No products available, stay tuned!.');
    })->with('genders');

});
