<?php

namespace App\Providers;

use App\Models\ProductModels\Category;
use App\Models\ProductModels\Product;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::if('active', function ($routeName) {
            return Route::is($routeName);
        });

        $this->registerRouteBindings();
        $this->registerViewComposers();
    }

    private function registerRouteBindings(): void
    {
        /*
         * {gender} → Category where depth = 'gender', matched by slug
         */
        Route::bind('gender', function (string $slug) {
            return Category::genders()
                ->active()
                ->where('slug', $slug)
                ->firstOrFail();
        });

        /*
         * {category} → Category where depth = 'category',
         * scoped to the already-resolved {gender}
         */
        Route::bind('category', function (string $slug) {
            $gender = request()->route('gender');

            return Category::categories()
                ->active()
                ->where('slug', $slug)
                ->where('parent_id', $gender->id)
                ->firstOrFail();
        });

        /*
         * {subcategory} → Category where depth = 'subcategory',
         * scoped to the already-resolved {category}
         */
        Route::bind('subcategory', function (string $slug) {
            $category = request()->route('category');

            return Category::subcategories()
                ->active()
                ->where('slug', $slug)
                ->where('parent_id', $category->id)
                ->firstOrFail();
        });

        /*
         * {product} — products table has no slug column.
         * We use the numeric ID but validate it belongs to the subcategory
         * so /men/jackets/leather/999 cannot show a dress.
         *
         * TIP: Add a `slug` column to products and change this to slug lookup
         * for SEO-friendly URLs like /men/jackets/leather/black-biker-jacket
         */
        Route::bind('product', function (string $slug) {
            $subcategory = request()->route('subcategory');

            return Product::with(['variants.size', 'variants.color', 'images', 'fit', 'brand'])
                ->where('slug', $slug)
                ->where('category_id', $subcategory->id)
                ->firstOrFail();
        });
    }

    private function registerViewComposers(): void
    {
        /*
         * Nav: all active genders with their active category children.
         * Cached for 60 min, busted by Category model events.
         */
        View::composer('components.layout.nav', function ($view) {
            // $genders = cache()->remember('nav_genders', now()->addMinutes(60), function () {
            //     return Category::genders()
            //         ->active()
            //         ->with('children')
            //         ->get();
            // });
            $genders = Category::genders()
                ->active()
                ->with('children.children')
                ->get();

            $view->with('navGenders', $genders);

        });
    }
}
