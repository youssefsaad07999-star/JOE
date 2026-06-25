<?php

use App\Models\ProductModels\Category;
use App\Models\ProductModels\Product;
use App\Models\ProductModels\ProductVariant;
use Database\Seeders\ProductSeeders\CategorySeeder;

dataset('genders', array_keys(CategorySeeder::$data));

describe('Gender page', function () {

    it('renders the gender index page successfully', function (string $gender) {
        $this->get(route('gender.index', ['gender' => $gender]))->assertOk();
    })->with('genders');

    it('shows each product name that belongs to the gender', function (string $gender) {

        $subCategory = Category::where('depth', 'subcategory')
            ->whereHas('parent.parent', fn ($q) => $q->where('slug', $gender))
            ->first();

        $products = Product::factory()->count(3)
            ->for($subCategory, 'category')
            ->has(ProductVariant::factory(), 'variants')
            ->create();

        $response = $this->get(route('gender.index', ['gender' => $gender]));

        $products->each(
            fn ($product) => $response->assertSeeText($product->name)
        ); // i think we can do foreach aswell

    })->with('genders');

    it('does not show the products of the opposite gender', function (string $gender) {
        $opposite = $gender === 'men' ? 'women' : 'men';

        $subCategory = Category::where('depth', 'subcategory')
            ->whereHas('parent.parent', fn ($q) => $q->where('slug', $opposite))
            ->first();

        $oppositeProducts = Product::factory()->count(3)
            ->for($subCategory, 'category')
            ->has(ProductVariant::factory(), 'variants')
            ->create();

        $response = $this->get(route('gender.index', ['gender' => $gender]));

        $oppositeProducts->each(
            fn ($product) => $response->assertDontSeeText($product->name)
        );
    })->with('genders');

    it('shows the category navigation links for the gender', function (string $gender) {
        $response = $this->get(route('gender.index', ['gender' => $gender]));

        Category::where('depth', 'category')
            ->whereHas('parent', fn ($q) => $q->where('slug', $gender))
            ->each(fn ($cat) => $response->assertSeeText($cat->name));

    })->with('genders');

    it('shows an empty state message when no products exist', function (string $gender) {
        $this->get(route('gender.index', ['gender' => $gender]))
            ->assertOk()
            ->assertViewHas('products', function ($products) {
                return $products->isEmpty();
            })
            ->assertSeeTextInOrder([
                'No products found.',
                'We are updating our stock. Please check back soon!',
            ]);
    })->with('genders');

});
