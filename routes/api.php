<?php

use App\Http\Controllers\Api\V1\UserTransactionsController;
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

Route::controller(UserTransactionsController::class)->prefix('v1')->group(function(){
    Route::get('user-transactions/migrate','migrate');
    Route::get('user-transactions/transactions','transactions_list');
    Route::get('user-transactions/users','users_list');
});
