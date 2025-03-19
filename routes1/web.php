<?php

use App\Http\Controllers\GiftController;
use App\Http\Controllers\GiftRedeemController;
use App\Http\Controllers\GiftRedemptionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\RedemptionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    return view('welcome');
});
Route::get('test', fn() => phpinfo());



Route::prefix('admin')->group(function () {

    // Login Routes (No middleware needed)
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);

    // Logout Route
    Route::post('logout', function () {
        Auth::logout();
        return redirect('/admin/login');
    })->name('logout');

    // Routes that require authentication
    Route::middleware(['auth'])->group(function () {
        // Profile Routes
        Route::get('profile', [LoginController::class, 'profile'])->name('admin.profile');
        Route::get('profile/edit', [LoginController::class, 'editProfile'])->name('profile.edit');
        // Route::post('profile/update', [LoginController::class, 'updateProfile'])->name('profile.update');

        // Slider Routes
        Route::get('sliders', [SliderController::class, 'index'])->name('admin.sliders.index');
        Route::get('sliders/create', [SliderController::class, 'create'])->name('admin.sliders.create');
        Route::post('sliders', [SliderController::class, 'store'])->name('admin.sliders.store');
        Route::get('sliders/{slider}/edit', [SliderController::class, 'edit'])->name('admin.sliders.edit');
        Route::put('sliders/{slider}', [SliderController::class, 'update'])->name('admin.sliders.update');
        Route::delete('sliders/{slider}', [SliderController::class, 'destroy'])->name('admin.sliders.destroy');

        // User Routes
        Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('admin.users.show');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::get('/users-status/{status?}', [UserController::class, 'index'])->name('admin.users');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

        // QR Routes
        Route::get('/qr/export-pdf', [QrController::class, 'exportPdf'])->name('admin.qr.exportPdf');
        Route::get('/qr', [QrController::class, 'index'])->name('admin.qr');
        Route::get('/used-qr', [QrController::class, 'getUsedQrCode'])->name('admin.usedQr');
        Route::get('/qr/create', [QrController::class, 'create'])->name('admin.qr.create');
        Route::post('/qr/storeBatch', [QrController::class, 'storeAndExportPdf'])->name('admin.qr.storeBatch');
        Route::post('/qr', [QrController::class, 'store'])->name('admin.qr.store');
        Route::get('/qr/{qr}', [QrController::class, 'show'])->name('admin.qr.show');
        Route::get('/qr/{qr}/edit', [QrController::class, 'edit'])->name('admin.qr.edit');
        Route::put('/qr/{qr}', [QrController::class, 'update'])->name('admin.qr.update');
        Route::delete('/qr/{qr}', [QrController::class, 'destroy'])->name('admin.qr.destroy');


        // Dashboard Route
        Route::get('/dashboard', [HomeController::class, 'index'])->name('admin.dashboard');
        Route::get('/transactions', [TransactionController::class, 'index'])->name('admin.transaction');
        Route::get('/products', [ProductController::class, 'index'])->name('admin.products');
        Route::get('/product/create', [ProductController::class, 'create'])->name('admin.product.create');
        Route::get('/products/{id}', [ProductController::class, 'show'])->name('admin.product.show');        Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('admin.product.edit');
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('admin.product.update');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('admin.product.destroy');

        Route::get('/gifts', [GiftController::class, 'index'])->name('admin.gift');
        Route::get('/gifts/create', [GiftController::class, 'create'])->name('admin.gift.create');
        Route::post('/gifts', [GiftController::class, 'store'])->name('admin.gift.store');
        Route::get('/gifts/{id}/edit', [GiftController::class, 'edit'])->name('admin.gift.edit');
        Route::put('/gifts/{id}', [GiftController::class, 'update'])->name('admin.gift.update');
        Route::delete('/gifts/{id}', [GiftController::class, 'destroy'])->name('admin.gift.destroy');

        Route::get('/redemption/{status?}', [RedemptionController::class, 'index'])->name('admin.redemption');

        Route::get('/redemption/{id}', [RedemptionController::class, 'show'])->name('admin.redemption.show');
        Route::post('/redemption/approve/{id}', [RedemptionController::class, 'approve'])->name('admin.redemption.approve');
        Route::post('/redemption/reject/{id}', [RedemptionController::class, 'reject'])->name('admin.redemption.reject');
        Route::delete('/redemption/{id}', [RedemptionController::class, 'delete'])->name('admin.redemption.delete');

        // Routes for Settings
        Route::get('/settings/edit', [SettingController::class, 'editSettings'])->name('admin.settings.edit');
        Route::put('/settings/update', [SettingController::class, 'updateSettings'])->name('admin.settings.update');
    });
});





Route::get('/register', [LoginController::class, 'showLoginForm'])->name('register');
Route::get('/password-request', [LoginController::class, 'showLoginForm'])->name('password.request');


Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return "All cache cleared!";
});
