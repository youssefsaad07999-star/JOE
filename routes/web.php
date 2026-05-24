<?php

// use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductControllers\CategoryController;
use App\Http\Controllers\ProductControllers\GenderController;
use App\Http\Controllers\ProductControllers\SubcategoryController;
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
});

Route::group(['middleware' => 'guest'], function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    // Route::post('/categories/{category}', [ProductController::class, 'byCategory']);

    Route::get('/login', [SessionsController::class, 'create'])->name('login');
    Route::post('/login', [SessionsController::class, 'store']);

});

// foreach (['men', 'women'] as $gender) {
//     Route::prefix($gender)
//         ->name("$gender.")
//         ->group(function () use ($gender) {
//             Route::get('/', [GenderController::class, 'show'])
//                 ->defaults('gender', $gender)
//                 ->name('index');

//             Route::get('/{category}', [CategoryController::class, 'show'])
//                 ->defaults('gender', $gender)
//                 ->name('category.show');

//             Route::get('/{category}/{subcategory}', [SubcategoryController::class, 'show'])
//                 ->defaults('gender', $gender)
//                 ->name('subcategory.show');
//         });
// }

Route::prefix('{gender}')
    ->where(['gender' => '[a-z][a-z0-9-]*'])
    ->name('gender.')
    ->group(function () {

        // Level 1: /men  /women  /unisex
        Route::get('/', [ProductController::class, 'genderIndex'])
            ->name('index');

        // Level 2: /men/jackets  /women/dresses
        Route::get('/{category}', [ProductController::class, 'categoryShow'])
            ->name('category.show');

        // Level 3: /men/jackets/leather
        Route::get('/{category}/{subcategory}', [ProductController::class, 'subcategoryShow'])
            ->name('subcategory.show');

        // Level 4: /men/jackets/leather/42
        // Note: {product} is an ID because products has no slug column.
        // Add slug to products table for SEO-friendly URLs.
        Route::get('/{category}/{subcategory}/{product}', [ProductController::class, 'productShow'])
            ->name('product.show');
    });
