<?php

// use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductControllers\CartItemController;
use App\Http\Controllers\ProductControllers\GenderController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionsController;
use App\Models\ProductModels\Category;
use Illuminate\Support\Facades\Route;

// Route::get('/products/{category}', [ProductController::class, 'byCategory']);

Route::get('/', [GenderController::class, 'index'])->name('home');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::group(['middleware' => 'auth'], function () {
    Route::post('/logout', [SessionsController::class, 'destroy']);

    Route::get('/orders', [OrderController::class, 'index'])->name('order.index');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
});

Route::group(['middleware' => 'guest'], function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    // Route::post('/categories/{category}', [ProductController::class, 'byCategory']);

    Route::get('/login', [SessionsController::class, 'create'])->name('login');
    Route::post('/login', [SessionsController::class, 'store']);

});

Route::get('/cart', [CartItemController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartItemController::class, 'store'])->name('cart.store');
Route::patch('/cart/{cartItem}', [CartItemController::class, 'update'])->name('cart.update');
Route::delete('/cart/{cartItem}', [CartItemController::class, 'destroy'])->name('cart.destroy');
Route::delete('/cart', [CartItemController::class, 'clear'])->name('cart.clear');

Route::prefix('{gender}')
    ->where(['gender' => '[a-z][a-z0-9-]*'])
    ->name('gender.')
    ->group(function () {

        Route::get('/', [ProductController::class, 'genderIndex'])
            ->name('index');

        Route::get('/{category}', [ProductController::class, 'categoryShow'])
            ->name('category.show');

        Route::get('/{category}/{subcategory}', [ProductController::class, 'subcategoryShow'])
            ->name('subcategory.show');

        Route::get('/{category}/{subcategory}/{product}', [ProductController::class, 'productShow'])
            ->name('product.show');
    });
