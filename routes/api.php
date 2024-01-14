<?php

use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\TransactionController;
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

// Route::post('/register', 'Auth\UserAuthController@register');
Route::post('/register', [UserAuthController::class, 'register']);
Route::post('/login', [UserAuthController::class, 'login']);
Route::post('/logout', [UserAuthController::class, 'logout']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('/transaction', [TransactionController::class, 'index']);
    Route::post('/store_transaction', [TransactionController::class, 'store_transaction']);
    Route::post('/store_transaction_status', [TransactionController::class, 'store_transaction_status']);
    Route::get('/transaction/{transaction}', [TransactionController::class, 'show']);
    Route::put('/transaction/{transaction}', [TransactionController::class, 'update']);
    Route::delete('/transaction/{transaction}', [TransactionController::class, 'destroy']);
});
