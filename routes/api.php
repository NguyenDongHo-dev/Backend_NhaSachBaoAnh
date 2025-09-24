<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RefreshTokenController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;

use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Http\Middleware\RefreshToken;

//user
Route::prefix('user')->group(function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login'])->name('login');


    Route::middleware(['admin_jwt'])->group(function () {
        Route::put('/updateByAdmin/{id}', [UserController::class, 'updateByAdmin']);
        Route::get('/allUser', [UserController::class, 'index']);
        Route::delete('/delete/{id}', [UserController::class, 'delete']);
        Route::post('/createUser', [UserController::class, 'createUser']);
        Route::get('/detailsByAdmin/{id}', [UserController::class, 'detailsByAdmin']);
    });

    Route::middleware(['user_jwt', 'jwt.auth'])->group(function () {
        Route::put('/update/{id}', [UserController::class, 'update']);
        Route::post('/logout', [UserController::class, 'logout']);
        Route::get('/details', [UserController::class, 'details']);
    });
});

//category
Route::prefix('category')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{slug}', [CategoryController::class, 'show']);
    Route::get('/type/{slug}', [CategoryController::class, 'type']);


    Route::middleware(['admin_jwt'])->group(function () {
        Route::post('/', [CategoryController::class, 'create']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
    });
});

//product
Route::prefix('product')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{slug}', [ProductController::class, 'show']);


    Route::middleware(['admin_jwt'])->group(function () {
        Route::post('/', [ProductController::class, 'store']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
    });
});



//wishlist
Route::prefix('wishlist')->group(function () {
    Route::middleware(['user_jwt', 'jwt.auth'])->group(function () {
        Route::get('/{id}', [WishlistController::class, 'index']);
        Route::get('/product/{id}', [WishlistController::class, 'productOfWishlist']);
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
        Route::get('/details/{id}', [OrderController::class, 'detailsByAdnin']);
        Route::put('/{id}', [OrderController::class, 'update']);
        Route::put('/cancelled/{id}', [OrderController::class, 'cancelled']);
    });

    Route::get('/', [OrderController::class, 'index']);
    Route::get('/{id}', [OrderController::class, 'detailOrder']);
    Route::post('/', [OrderController::class, 'store']);
    Route::delete('/{id}', [OrderController::class, 'updateToCancelled']);
});

Route::post('/refresh-token', [RefreshTokenController::class, 'refresh']);


Route::middleware(['admin_jwt'])->group(
    function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);
    }
);
