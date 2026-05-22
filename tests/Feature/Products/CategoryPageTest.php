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

describe('Category page', function () {

    it('renders the category page successfully', function (string $gender) {
        $category = Category::where('depth', 'category')
            ->whereHas('parent', fn ($q) => $q->where('slug', $gender))
            ->first();

        $this->get(route("$gender.category.show", $category))
            ->assertOk();
    })->with('genders');

    it('returns 404 for unknown category', function (string $gender) {
        $this->get(route("$gender.category.show", 'not-existed'))
            ->assertNotFound();
    })->with('genders');

    it('return 404 when the category belongs to the opposite gender', function (string $gender) {
        $opposite = $gender === 'men' ? 'women' : 'men';

        $oppositeCategory = Category::where('depth', 'category')
            ->whereHas('parent', fn ($q) => $q->where('slug', $opposite))
            ->first();

        $this->get(route("$gender.category.show", $oppositeCategory))
            ->assertNotFound();
    })->with('genders');

    it('shows each product name that belongs to the category', function (string $gender) {
        $category = Category::where('depth', 'category')
            ->whereHas('parent', fn ($q) => $q->where('slug', $gender))
            ->with('children')
            ->first();

        $subcategory = $category->children->first();

        $products = Product::factory()->count(2)
            ->for($subcategory, 'category')
            ->has(ProductVariant::factory(), 'variants')
            ->create();

        $response = $this->get(route("$gender.category.show", $category));

        $products->each(
            fn ($product) => $response->assertSeeText($product->name)
        ); // here we need to add more details

    })->with('genders');

    it('does not show products from a sibling category', function (string $gender) {

        $mainCat = Category::where('depth', 'category')
            ->whereHas('parent', fn ($q) => $q->where('slug', $gender))
            ->with('children')
            ->get();

        $target = $mainCat->first();

        $sibling = $mainCat->skip(1)->first();

        $siblingSubcat = $sibling->children->first();

        $products = Product::factory()->count(2)
            ->for($siblingSubcat, 'category')
            ->has(ProductVariant::factory(), 'variants')
            ->create();

        $response = $this->get(route("$gender.category.show", $target));

        $products->each(
            fn ($product) => $response->assertDontSeeText($product->name)
        );

    })->with('genders');

    it('does not show producs from the opposite gender same category name', function (string $gender) {

        $opposite = $gender === 'men' ? 'women' : 'men';

        $category = Category::where('depth', 'category')
            ->whereHas('parent', fn ($q) => $q->where('slug', $gender))
            ->first();

        $oppositeCategory = Category::where('depth', 'category')
            ->whereHas('parent', fn ($q) => $q->where('slug', $opposite))
            ->first();

        $oppositeProduct = Product::factory()
            ->for($oppositeCategory->children()->first(), 'category')
            ->has(ProductVariant::factory(), 'variants')
            ->create();

        $this->get(route("$gender.category.show", $category))
            ->assertDontSeeText($oppositeProduct->name);

    })->with('genders');

    it('shows the subcategories navigation links of a category', function (string $gender) {

        $category = Category::where('depth', 'category')
            ->whereHas('parent', fn ($q) => $q->where('slug', $gender))
            ->with('children')
            ->first();

        $response = $this->get(route("$gender.category.show", $category));

        $category->children
            // ->where('is_active', true) we can add this but we don't need as the test database is different
            ->each(
                fn ($subcategory) => $response->assertSeeText($subcategory->name)
            );

    })->with('genders');

    it('shows an empty state message when no products exist in the category', function (string $gender) {

        $category = Category::where('depth', 'category')
            ->whereHas('parent', fn ($q) => $q->where('slug', $gender))
            ->first();

        $this->get(route("$gender.category.show", $category))
            ->assertSeeText('No products available, stay tuned!.');

    })->with('genders');

});
