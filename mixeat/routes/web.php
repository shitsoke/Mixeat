<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\SupervisorController;
use App\Http\Controllers\Admin\SupervisorManagementController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public & Guest Routes
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| Email Verification Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // 1. Notice View: Rendered when an unverified user accesses a 'verified' route
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    // 2. Verification Link Handler: Link sent to user email
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect()->route('home')->with('status', 'Your email address has been verified!');
    })->middleware('signed')->name('verification.verify');

    // 3. Resend Verification Link
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', 'A fresh verification link has been sent to your email address.');
    })->middleware('throttle:6,1')->name('verification.send');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Profile, Password Management & Logout)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [StorefrontController::class, 'profile'])->name('profile');
    Route::get('/profile/edit', [AuthController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Password Management Routes
    Route::get('/profile/password', [AuthController::class, 'showChangePasswordForm'])->name('password.change');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('password.update');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Customer Ordering Routes (Protected by Verified Middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/orders', [StorefrontController::class, 'placeOrder'])->name('orders.place');
    Route::get('/checkout', [StorefrontController::class, 'checkout'])->name('checkout');
    Route::get('/order-confirmation', [StorefrontController::class, 'orderConfirmation'])->name('order-confirmation');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Separated by Role)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // 1. Marketing Admin Routes
    Route::middleware(['role:marketing'])->group(function () {
        // Promotions
        Route::get('/promotion', [AdminController::class, 'promotion'])->name('promotion');
        Route::put('/promotion', [AdminController::class, 'updatePromotion'])->name('promotion.update');

        // Master Menu Products
        Route::get('/products', [AdminController::class, 'index'])->name('products.index');
        Route::get('/products/create', [AdminController::class, 'create'])->name('products.create');
        Route::post('/products', [AdminController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [AdminController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [AdminController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [AdminController::class, 'destroy'])->name('products.destroy');
        Route::patch('/products/{product}/branches/{branch}', [AdminController::class, 'toggleAvailability'])->name('products.availability');

        // Branch Management
        Route::get('/branches', [AdminController::class, 'branches'])->name('branches.index');
        Route::get('/branches/create', [AdminController::class, 'createBranch'])->name('branches.create');
        Route::post('/branches', [AdminController::class, 'storeBranch'])->name('branches.store');
        Route::get('/branches/{branch}/edit', [AdminController::class, 'editBranch'])->name('branches.edit');
        Route::put('/branches/{branch}', [AdminController::class, 'updateBranch'])->name('branches.update');
        Route::delete('/branches/{branch}', [AdminController::class, 'destroyBranch'])->name('branches.destroy');

        // Supervisor Assignment Management
        Route::get('/supervisors/manage', [SupervisorManagementController::class, 'index'])->name('supervisors.manage');
        Route::post('/supervisors/assign', [SupervisorManagementController::class, 'assignBranch'])->name('supervisors.assign');
        Route::delete('/supervisors/remove/{user}', [SupervisorManagementController::class, 'removeSupervisor'])->name('supervisors.remove');

        // CMS Pages (About & Contact)
        Route::get('/cms/about', [CmsController::class, 'editAbout'])->name('cms.about');
        Route::post('/cms/about', [CmsController::class, 'updateAbout'])->name('cms.about.update');
        Route::get('/cms/contact', [CmsController::class, 'editContact'])->name('cms.contact');
        Route::post('/cms/contact', [CmsController::class, 'updateContact'])->name('cms.contact.update');
    });

    // 2. Branch Supervisor Routes
    Route::middleware(['role:supervisor'])->group(function () {
        Route::get('/supervisors', [SupervisorController::class, 'index'])->name('supervisors.index');
        Route::get('/supervisors/export-daily-sales', [SupervisorController::class, 'exportDailySales'])->name('supervisors.export-daily-sales');
        Route::patch('/supervisors/{product}/toggle', [SupervisorController::class, 'toggleStatus'])->name('supervisors.toggle');
        
        // Order Status Actions
        Route::patch('/supervisors/orders/{order}/accept', [SupervisorController::class, 'acceptOrder'])->name('supervisors.orders.accept');
        Route::patch('/supervisors/orders/{order}/ready', [SupervisorController::class, 'markReadyForPickup'])->name('supervisors.orders.ready');
        Route::patch('/supervisors/orders/{order}/complete', [SupervisorController::class, 'markCompleted'])->name('supervisors.orders.complete');
        Route::patch('/supervisors/orders/{order}/decline', [SupervisorController::class, 'declineOrder'])->name('supervisors.orders.decline');
    });

});