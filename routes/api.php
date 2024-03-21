<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;
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

Route::post('/createuser', [UserController::class, 'createuser']);
Route::post('/apply_for_loan', [UserController::class, 'apply_for_loan']);
Route::put('/update_loan_status', [UserController::class, 'update_loan_status']);
Route::put('/loan_transitioning', [UserController::class, 'loan_transitioning']);

Route::get('/pay', [PaymentController::class, 'redirectToGateway'])->name('pay');
Route::get('/payment/callback', [PaymentController::class, 'handleGatewayCallback']);
Route::post('/repayments', [PaymentController::class, 'initiateRepayment']);
