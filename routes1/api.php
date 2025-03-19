<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GiftController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\RedemptionController;
use App\Http\Controllers\TransactionController;
use App\Http\Middleware\AuthToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// routes/web.php
Route::get('/test', function () {
    return response()->json(['message' => 'Api working fine..']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);

Route::middleware(['auth:sanctum', AuthToken::class])->group(function () {
    Route::get('/dashboard', [AppController::class, 'dashboard']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/profile-update', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);


    Route::post('/qr/redeem', [QrController::class, 'useQrCode']);

    Route::post('/redemption-request', [RedemptionController::class, 'redeemRequestSubmit']);
    // Route::get('/redemption-request', [RedemptionController::class, 'showRedeemRequest']);

    Route::get('/gifts', [GiftController::class, 'indexApi']);
    Route::post('/transactions', [TransactionController::class, 'indexApi']);
    Route::post('/gift/transactions', [TransactionController::class, 'getGiftHistory']);
    Route::post('/cash/transactions', [TransactionController::class, 'getCashHistory']);
    Route::post('/product/transactions', [TransactionController::class, 'getProductHistory']);

    Route::post('/products', [ProductController::class, 'getProducts']);
});

Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);
