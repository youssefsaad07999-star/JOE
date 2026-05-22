<?php

// use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductControllers\CategoryController;
use App\Http\Controllers\ProductControllers\GenderController;
use App\Http\Controllers\ProductControllers\SubcategoryController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionsController;
use App\Models\ProductModels\Category;
use Illuminate\Support\Facades\Route;

// Route::get('/products/{category}', [ProductController::class, 'byCategory']);

Route::get('/', [GenderController::class, 'index']);

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

foreach (['men', 'women'] as $gender) {
    Route::prefix($gender)
        ->name("$gender.")
        ->group(function () use ($gender) {
            Route::get('/', [GenderController::class, 'show'])
                ->defaults('gender', $gender)
                ->name('index');

            Route::get('/{category}', [CategoryController::class, 'show'])
                ->defaults('gender', $gender)
                ->name('category.show');

            Route::get('/{category}/{subcategory}', [SubcategoryController::class, 'show'])
                ->defaults('gender', $gender)
                ->name('subcategory.show');
        });
}
