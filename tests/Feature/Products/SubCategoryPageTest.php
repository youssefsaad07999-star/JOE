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

dataset('genders', array_keys(CategorySeeder::$data));

describe('Subcategory page', function () {

    it('renders the subcategory page successfully', function (string $gender) {
        $category = Category::where('depth', 'category')
            ->whereHas('parent', fn ($q) => $q->where('slug', $gender))
            ->with('children')
            ->first();

        $this->get(route('gender.subcategory.show', [
            'gender' => $gender,
            'category' => $category->slug,
            'subcategory' => $category->children->first()->slug,
        ]))->assertOk();

    })->with('genders');

    it('returns 404 for unknown subcategory', function (string $gender) {
        $category = Category::where('depth', 'category')
            ->whereHas('parent', fn ($q) => $q->where('slug', $gender))
            ->first();

        $this->get(route('gender.subcategory.show', [
            'gender' => $gender,
            'category' => $category->slug,
            'subcategory' => 'non-existent',
        ]))->assertNotFound();

    })->with('genders');

    it('returns 404 when the subcategory does not belong to the category', function (string $gender) {

        $category = Category::where('depth', 'category')
            ->whereHas('parent', fn ($q) => $q->where('slug', $gender))
            ->first();

        $uniqueGender = Category::genders()
            ->where('slug', $gender)
            ->first();
        $uniqueCategory = Category::create([
            'name' => 'Unique to '.$gender,
            'slug' => 'unique-to-'.$gender,
            'depth' => 'category',
            'parent_id' => $uniqueGender->id,
            'is_active' => true,
        ]);

        $uniqueSubcategory = Category::create([
            'name' => 'Unique to '.$uniqueCategory->name,
            'slug' => 'unique-to-'.$uniqueCategory->slug,
            'depth' => 'subcategory',
            'parent_id' => $uniqueCategory->id,
            'is_active' => true,
        ]);

        $this->get(route('gender.subcategory.show', [
            'gender' => $gender,
            'category' => $category->slug,
            'subcategory' => $uniqueSubcategory->slug,
        ]))->assertNotFound();
        // 200
    })->with('genders');

    it('return 404 when subcategory belongs to the opposite gender', function (string $gender) {
        $opposite = $gender === 'men' ? 'women' : 'men';

        $oppositeCategory = Category::where('depth', 'category')
            ->whereHas('parent', fn ($q) => $q->where('slug', $opposite))
            ->first();

        $category = Category::where('depth', 'category')
            ->whereHas('parent', fn ($q) => $q->where('slug', $gender))
            ->first();

        $uniqueSubcategory = Category::create([
            'name' => 'Unique to '.$oppositeCategory->name,
            'slug' => 'unique-to-'.$oppositeCategory->slug,
            'depth' => 'subcategory',
            'parent_id' => $oppositeCategory->id,
            'is_active' => true,
        ]);

        $this->get(route('gender.subcategory.show', [
            'gender' => $gender,
            'category' => $category->slug,
            'subcategory' => $uniqueSubcategory->slug, // Attempt to read property "slug" on null
        ]))->assertNotFound();

    })->with('genders');

    it('does not show product at the same subcategory for the opposite gender', function (string $gender) {
        $opposite = $gender === 'men' ? 'women' : 'men';

        $category = Category::where('depth', 'category')
            ->whereHas('parent', fn ($q) => $q->where('slug', $gender))
            ->first();

        $oppositeSubcategory = Category::where('depth', 'subcategory')
            ->whereHas('parent.parent', fn ($q) => $q->where('slug', $opposite))
            ->first();

        $product = Product::factory()
            ->for($oppositeSubcategory, 'category')
            ->has(ProductVariant::factory(), 'variants')
            ->create();

        $this->get(route('gender.subcategory.show', [
            'gender' => $gender,
            'category' => $category->slug,
            'subcategory' => $oppositeSubcategory->slug,
        ]))->assertDontSeeText($product->name);
    })->with('genders');

    it('does not show a product from a sibling subcategory', function (string $gender) {
        $category = Category::where('depth', 'category')
            ->whereHas('parent', fn ($q) => $q->where('slug', $gender))
            ->with('children')
            ->first();

        $siblingSubcategory = $category->children->skip(1)->first();

        $siblingProduct = Product::factory()
            ->for($siblingSubcategory, 'category')
            ->has(ProductVariant::factory(), 'variants')
            ->create();

        $this->get(route('gender.subcategory.show', [
            'gender' => $gender,
            'category' => $category->slug,
            'subcategory' => $category->children->first()->slug,
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

        $response = $this->get(route('gender.subcategory.show', [
            'gender' => $gender,
            'category' => $category->slug,
            'subcategory' => $subcategory->slug,
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

        $this->get(route('gender.subcategory.show', [
            'gender' => $gender,
            'category' => $category->slug,
            'subcategory' => $category->children->first()->slug,
        ]))->assertSeeText('No products available, stay tuned!.');
    })->with('genders');

});
