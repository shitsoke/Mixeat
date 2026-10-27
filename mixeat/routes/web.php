<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\CateringController as AdminCateringController;
use App\Http\Controllers\Admin\Marketing\CateringManagementController;
use App\Http\Controllers\Admin\SupervisorController;
use App\Http\Controllers\Admin\SupervisorManagementController;
use App\Http\Controllers\CateringInquiryController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\Admin\JobController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public & Guest Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
Route::get('/catering', [StorefrontController::class, 'catering'])->name('catering');
Route::get('/menu', [StorefrontController::class, 'menu'])->name('menu');
Route::get('/featured', [StorefrontController::class, 'featured'])->name('featured');
Route::get('/featured/{promotion}', [StorefrontController::class, 'featuredPromotion'])->name('featured.show');
Route::get('/products/{id}', [StorefrontController::class, 'product'])->name('product');
Route::post('/cart/{id}', [StorefrontController::class, 'addToCart'])->name('cart.add');
Route::patch('/cart/{id}/quantity', [StorefrontController::class, 'updateCartQuantity'])->name('cart.quantity');
Route::delete('/cart/{id}', [StorefrontController::class, 'removeFromCart'])->name('cart.remove');
Route::get('/branches', [StorefrontController::class, 'branches'])->name('branches');
Route::post('/branches/select', [StorefrontController::class, 'selectBranchFromDropdown'])->name('branches.select-dropdown');
Route::post('/branches/{branch}/select', [StorefrontController::class, 'selectBranch'])->name('branches.select');
Route::get('/cart', [StorefrontController::class, 'cart'])->name('cart');
Route::get('/orders/live', [StorefrontController::class, 'liveOrders'])->middleware('auth')->name('orders.live');
Route::get('/orders', [StorefrontController::class, 'orders'])->name('orders');
Route::get('/orders/{id}', [StorefrontController::class, 'orderDetail'])->name('order-detail');
Route::get('/about', [StorefrontController::class, 'about'])->name('about');
Route::get('/contact', [StorefrontController::class, 'contact'])->name('contact');
Route::get('/careers', [CareerController::class, 'index'])->name('careers');
Route::get('/careers/{job}/apply', [CareerController::class, 'showApply'])->name('careers.apply.form');
Route::post('/careers/{job}/apply', [CareerController::class, 'apply'])->name('careers.apply');
Route::post('/catering/inquiries', [CateringInquiryController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('catering.inquiries.store');

Route::middleware('guest')->group(function () {
    Route::get('/supervisor/login', fn () => redirect()->route('login'));
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/auth/{provider}/redirect', [AuthController::class, 'redirectToProvider'])
        ->whereIn('provider', ['google', 'facebook'])
        ->name('social.redirect');
    Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback'])
        ->whereIn('provider', ['google', 'facebook'])
        ->name('social.callback');
    Route::get('/oauth/{provider}/callback', [AuthController::class, 'handleProviderCallback'])
        ->whereIn('provider', ['google', 'facebook']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
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
Route::middleware(['auth'])->group(function () {
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

    Route::middleware(['role:marketing,marketing_admin'])->prefix('marketing')->name('marketing.')->group(function () {
        Route::get('/dashboard', [CateringManagementController::class, 'dashboard'])->name('dashboard');
        Route::get('/catering-bookings', [CateringManagementController::class, 'bookings'])->name('catering-bookings');
        Route::patch('/catering-bookings/{booking}/status', [CateringManagementController::class, 'updateBookingStatus'])->name('catering-bookings.status');
        Route::get('/catering-calendar', [CateringManagementController::class, 'calendar'])->name('catering-calendar');
        Route::patch('/catering-calendar/{booking}', [CateringManagementController::class, 'updateCalendarBooking'])->name('catering-calendar.update');

        Route::get('/catering-partners', [CateringManagementController::class, 'partners'])->name('catering-partners');
        Route::post('/catering-partners', [CateringManagementController::class, 'storePartner'])->name('catering-partners.store');
        Route::put('/catering-partners/{partner}', [CateringManagementController::class, 'updatePartner'])->name('catering-partners.update');
        Route::delete('/catering-partners/{partner}', [CateringManagementController::class, 'deletePartner'])->name('catering-partners.delete');
    });

    Route::middleware(['role:marketing,marketing_admin'])->group(function () {
        Route::get('/catering', [AdminCateringController::class, 'marketing'])->name('catering.marketing');
        Route::get('/catering/content', [AdminCateringController::class, 'content'])->name('catering.content');
        Route::put('/catering/content', [AdminCateringController::class, 'updateContent'])->name('catering.content.update');
    });

    // 1. Marketing Admin Routes
    Route::middleware(['role:marketing,marketing_admin'])->group(function () {
        // Promotions
        Route::get('/promotion', [AdminController::class, 'promotion'])->name('promotion');
        Route::put('/promotion', [AdminController::class, 'updatePromotion'])->name('promotion.update');

        Route::get('/promotions', [AdminController::class, 'promotions'])->name('promotions.index');
        Route::get('/promotions/create', [AdminController::class, 'createPromotion'])->name('promotions.create');
        Route::post('/promotions', [AdminController::class, 'storePromotion'])->name('promotions.store');
        Route::get('/promotions/{promotion}/edit', [AdminController::class, 'editPromotion'])->name('promotions.edit');
        Route::put('/promotions/{promotion}', [AdminController::class, 'updatePromotionPost'])->name('promotions.update');
        Route::delete('/promotions/{promotion}', [AdminController::class, 'destroyPromotion'])->name('promotions.destroy');

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

        Route::resource('jobs', JobController::class)->names('jobs');
        Route::patch('/jobs/{job}/toggle', [JobController::class, 'toggle'])->name('jobs.toggle');
        Route::get('/jobs/applications/{application}/resume', [JobController::class, 'downloadResume'])->name('jobs.resume');
    });

    // 2. Branch Supervisor Routes
    Route::middleware(['role:supervisor'])->group(function () {
        Route::get('/supervisors', [SupervisorController::class, 'index'])->name('supervisors.index');
        Route::get('/supervisors/orders/live', [SupervisorController::class, 'liveOrders'])->name('supervisors.orders.live');
        Route::get('/supervisors/export-daily-sales', [SupervisorController::class, 'exportDailySales'])->name('supervisors.export-daily-sales');
        Route::patch('/supervisors/{product}/toggle', [SupervisorController::class, 'toggleStatus'])->name('supervisors.toggle');
        
        // Order Status Actions
        Route::patch('/supervisors/orders/{order}/accept', [SupervisorController::class, 'acceptOrder'])->name('supervisors.orders.accept');
        Route::patch('/supervisors/orders/{order}/ready', [SupervisorController::class, 'markReadyForPickup'])->name('supervisors.orders.ready');
        Route::patch('/supervisors/orders/{order}/complete', [SupervisorController::class, 'markCompleted'])->name('supervisors.orders.complete');
        Route::patch('/supervisors/orders/{order}/decline', [SupervisorController::class, 'declineOrder'])->name('supervisors.orders.decline');
        
        // Supervisor Notifications
        Route::get('/supervisors/notifications', [SupervisorController::class, 'notifications'])->name('supervisors.notifications');
        Route::post('/supervisors/notifications/{id}/read', [SupervisorController::class, 'markNotificationRead'])->name('supervisors.notifications.read');
        Route::get('/supervisors/notifications/{id}/order', [SupervisorController::class, 'viewNotificationOrder'])->name('supervisors.notifications.order');
    });

});
