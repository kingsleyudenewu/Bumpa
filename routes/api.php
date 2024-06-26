<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FlutterwaveWebhookController;
use App\Http\Controllers\PaystackWebhookController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('wallet')->group(function () {
        Route::get('/balance', [WalletController::class, 'getWalletsBalance'])->name('wallet.balance');
        Route::post('/transfer', [WalletController::class, 'walletTransfer'])
            ->name('wallet.transfer')
            ->middleware('auth.transaction');
        Route::post('/fund-account', [WalletController::class, 'fundWallet'])
            ->name('wallet.transfer');
        Route::get('transactions', [TransactionController::class, 'getTransactions'])->name('transactions');
    });

    Route::get('/banks', [WithdrawalController::class, 'getBanks'])->name('withdraw.bank');
    Route::get('/bank/accounts', [WithdrawalController::class, 'getAllBankAccounts'])->name('bank.account');
    Route::post('/verify-bank-account', [WithdrawalController::class, 'verifyBankAccount'])
        ->name('withdraw.verify.account')
        ->middleware('auth.transaction');
    Route::post('/create-recipient', [WithdrawalController::class, 'createRecipient'])
        ->name('withdraw.create.recipient')
        ->middleware('auth.transaction');
    Route::post('/initiate-transfer', [WithdrawalController::class, 'initiateTransfer'])
        ->name('withdraw.initiate.transfer')
        ->middleware('auth.transaction');
});
