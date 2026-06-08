<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\ProductModels\CartItem;
use App\Models\ProductModels\Category;
use App\Models\ProductModels\Product;
use App\Models\User;
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

        Blade::if('admin', function () {
            return auth()->check()
            && auth()->user()->role === 'admin';
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
                ->first();
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

        Route::bind('adminCategory', function (string $id) {
            return Category::findOrFail($id); // plain ID lookup, no scope
        });

        Route::bind('product', function (string $slug) {
            $subcategory = request()->route('subcategory');

            if ($subcategory) {
                return Product::with(['variants.size', 'variants.color', 'images', 'primaryImage', 'fit', 'brand'])
                    ->where('slug', $slug)
                    ->where('category_id', $subcategory->id)
                    ->first();
            }

            return Product::with(['variants.size', 'variants.color', 'images', 'primaryImage', 'fit', 'brand'])
                ->where('slug', $slug)
                ->first();
        });

        Route::bind('order', function (int $id) {
            return Order::with([
                'variants.product.fit',
                'variants.product.images',
                'variants.size',
                'variants.color',
                'address',
                'payment',
                'user',
            ])->findOrFail($id);

        });
        // orders addresses
        Route::bind('user', function (string $id) {
            return User::with(['addresses'])->findOrFail($id);
        });
    }

    private function registerViewComposers(): void
    {
        View::composer('components.admin.nav', function ($view) {
            $view->with('pendingCount', Order::where('status', 'pending')->count());
        });

        View::composer('components.layout.layout', function ($view) {
            $query = CartItem::query();

            $cartItems = auth()->check()
            ? $query->forUser(auth()->id())->with('variant.product.images')->get()
            : $query->forSession(session()->getId())->with('variant.product.images')->get();

            $cartTotal = $cartItems->sum->line_total;

            $view->with(compact('cartItems', 'cartTotal'));
        });

        View::composer('components.layout.nav', function ($view) {

            $genders = Category::genders()
                ->active()
                ->with([
                    'children' => function ($query) {
                        $query->active()->with([
                            'children' => function ($subQuery) {
                                $subQuery->active();
                            },
                        ]);
                    },
                ])
                ->get();

            $view->with('navGenders', $genders);

        });
    }
}
