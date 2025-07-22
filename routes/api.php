<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;

use Illuminate\Support\Facades\Route;

//user
Route::prefix('user')->group(function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login'])->name('login');


    Route::middleware(['admin_jwt'])->group(function () {
        Route::get('/allUser', [UserController::class, 'index']);
        Route::delete('/delete/{id}', [UserController::class, 'delete']);
        Route::get('/details/{id}', [UserController::class, 'details']);
    });

    Route::middleware(['user_jwt'])->group(function () {
        Route::put('/update/{id}', [UserController::class, 'update']);
        Route::post('/logout', [UserController::class, 'logout']);
        Route::get('/{id}', [UserController::class, 'detailsMe']);
    });
});

//category
Route::prefix('category')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::get('/type/{id}', [CategoryController::class, 'type']);


    Route::middleware(['admin_jwt'])->group(function () {
        Route::post('/', [CategoryController::class, 'create']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
    });
});

//product
Route::prefix('product')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'show']);


    Route::middleware(['admin_jwt'])->group(function () {
        Route::post('/', [ProductController::class, 'store']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
    });
});

//cart
Route::prefix('cart')->group(function () {
    Route::middleware(['user_jwt'])->group(function () {
        Route::get('/{id}', [CartController::class, 'index']);
        Route::post('/{id}', [CartController::class, 'store']);
        Route::delete('/{id}', [CartController::class, 'destroy']);
    });
});

//wishlist
Route::prefix('wishlist')->group(function () {
    Route::middleware(['user_jwt'])->group(function () {
        Route::get('/{id}', [WishlistController::class, 'index']);
        Route::post('/{id}', [WishlistController::class, 'store']);
        Route::delete('/{id}', [WishlistController::class, 'destroy']);
    });
});


//review
Route::prefix('review')->group(function () {
    Route::get('/allReviews', [ReviewController::class, 'allReviews']);
    Route::delete('/deleteReview/{id}', [ReviewController::class, 'deleteReview']);


    Route::get('/{id}', [ReviewController::class, 'index']);




    Route::middleware(['jwt.auth'])->group(function () {
        Route::post('/', [ReviewController::class, 'store']);
        Route::put('/{id}', [ReviewController::class, 'update']);
        Route::delete('/{id}', [ReviewController::class, 'destroy']);
    });
});



//order
Route::middleware(['jwt.auth'])->prefix('order')->group(function () {

    Route::middleware(['admin_jwt'])->group(function () {
        Route::get('/allOrder', [OrderController::class, 'allOrder']);
        Route::put('/{id}', [OrderController::class, 'update']);
        Route::delete('/deteleAdmin/{id}', [OrderController::class, 'deteleAdmin']);
    });

    Route::get('/', [OrderController::class, 'index']);
    Route::get('/{id}', [OrderController::class, 'detailOrder']);
    Route::post('/', [OrderController::class, 'store']);
    Route::delete('/{id}', [OrderController::class, 'destroy']);
});
