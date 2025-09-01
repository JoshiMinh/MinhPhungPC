<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BuildController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ForgotController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AdminController;

// Public routes
Route::get('/home', [HomeController::class, 'index']);
Route::get('/build', [BuildController::class, 'index']);
Route::get('/cart', [CartController::class, 'index']);
Route::get('/account', [AccountController::class, 'index']);
Route::get('/forgot', [ForgotController::class, 'index']);
Route::get('/search', [SearchController::class, 'search']);
Route::get('/product/{id}', [ProductController::class, 'show']);
Route::get('/category/{type}', [CategoryController::class, 'show']);

// Admin routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/products', [AdminController::class, 'manageProducts'])->name('admin.products');
    Route::get('/admin/users', [AdminController::class, 'manageUsers'])->name('admin.users');
    Route::get('/admin/product/edit/{id}', [AdminController::class, 'editProduct'])->name('admin.product.edit');
    Route::post('/admin/product/update', [AdminController::class, 'updateProduct'])->name('admin.product.update');
    Route::post('/admin/product/delete', [AdminController::class, 'deleteProduct'])->name('admin.product.delete');
    Route::post('/admin/order/update', [AdminController::class, 'updateOrder'])->name('admin.order.update');
});

// Admin index route
Route::get('/admin', [AdminController::class, 'index']);