<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/menu', [StorefrontController::class, 'menu'])->name('menu');
Route::get('/featured', [StorefrontController::class, 'featured'])->name('featured');
Route::get('/products/{id}', [StorefrontController::class, 'product'])->name('product');
Route::post('/cart/{id}', [StorefrontController::class, 'addToCart'])->name('cart.add');
Route::patch('/cart/{id}/quantity', [StorefrontController::class, 'updateCartQuantity'])->name('cart.quantity');
Route::delete('/cart/{id}', [StorefrontController::class, 'removeFromCart'])->name('cart.remove');
Route::get('/branches', [StorefrontController::class, 'branches'])->name('branches');
Route::post('/branches/select', [StorefrontController::class, 'selectBranchFromDropdown'])->name('branches.select-dropdown');
Route::post('/branches/{branch}/select', [StorefrontController::class, 'selectBranch'])->name('branches.select');
Route::get('/cart', [StorefrontController::class, 'cart'])->name('cart');
Route::get('/orders', [StorefrontController::class, 'orders'])->name('orders');
Route::get('/orders/{id}', [StorefrontController::class, 'orderDetail'])->name('order-detail');
Route::get('/about', [StorefrontController::class, 'about'])->name('about');
Route::get('/contact', [StorefrontController::class, 'contact'])->name('contact');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [StorefrontController::class, 'checkout'])->name('checkout');
    Route::get('/order-confirmation', [StorefrontController::class, 'orderConfirmation'])->name('order-confirmation');
    Route::get('/profile', [StorefrontController::class, 'profile'])->name('profile');
    Route::get('/profile/edit', [AuthController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/promotion', [AdminController::class, 'promotion'])->name('promotion');
    Route::put('/promotion', [AdminController::class, 'updatePromotion'])->name('promotion.update');
    Route::get('/products', [AdminController::class, 'index'])->name('products.index');
    Route::get('/products/create', [AdminController::class, 'create'])->name('products.create');
    Route::post('/products', [AdminController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [AdminController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [AdminController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [AdminController::class, 'destroy'])->name('products.destroy');
    Route::patch('/products/{product}/branches/{branch}', [AdminController::class, 'toggleAvailability'])->name('products.availability');
    Route::get('/branches', [AdminController::class, 'branches'])->name('branches.index');
    Route::get('/branches/create', [AdminController::class, 'createBranch'])->name('branches.create');
    Route::post('/branches', [AdminController::class, 'storeBranch'])->name('branches.store');
    Route::get('/branches/{branch}/edit', [AdminController::class, 'editBranch'])->name('branches.edit');
    Route::put('/branches/{branch}', [AdminController::class, 'updateBranch'])->name('branches.update');
    Route::delete('/branches/{branch}', [AdminController::class, 'destroyBranch'])->name('branches.destroy');
});
