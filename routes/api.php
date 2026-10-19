<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Api\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Api\Seller\OrderController as SellerOrderController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;

// ==========================
// Routes Publik (tidak perlu login)
// ==========================

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{slug}', [CategoryController::class, 'show']);

Route::get('/stores/{slug}', [StoreController::class, 'show']);

// ==========================
// Routes yang Butuh Login (pakai token)
// ==========================

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);

    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);

    // ==========================
    // Routes Seller
    // ==========================
    Route::prefix('seller')->group(function () {
        Route::get('/dashboard', [SellerDashboardController::class, 'index']);
        Route::post('/store', [SellerDashboardController::class, 'createStore']);
                Route::put('/store', [SellerDashboardController::class, 'updateStore']);
        Route::get('/products', [SellerProductController::class, 'index']);
        Route::post('/products', [SellerProductController::class, 'store']);
        Route::put('/products/{id}', [SellerProductController::class, 'update']);
        Route::delete('/products/{id}', [SellerProductController::class, 'destroy']);

        Route::get('/orders', [SellerOrderController::class, 'index']);
        Route::put('/orders/{id}/status', [SellerOrderController::class, 'updateStatus']);
    });

    // ==========================
    // Routes Admin
    // ==========================
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);

        Route::get('/users', [AdminUserController::class, 'index']);
        Route::get('/users/{id}', [AdminUserController::class, 'show']);
        Route::delete('/users/{id}', [AdminUserController::class, 'destroy']);

        Route::get('/categories', [AdminCategoryController::class, 'index']);
        Route::post('/categories', [AdminCategoryController::class, 'store']);
        Route::put('/categories/{id}', [AdminCategoryController::class, 'update']);
        Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy']);

        Route::get('/products', [AdminProductController::class, 'index']);
        Route::delete('/products/{id}', [AdminProductController::class, 'destroy']);

        Route::get('/orders', [AdminOrderController::class, 'index']);
    });
});