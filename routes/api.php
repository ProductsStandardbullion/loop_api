<?php

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
    return redirect('https://loopoptions.com/');
});


Route::prefix('v1')->group(function () {
    Route::post('user/new',[\App\Http\Controllers\Auth\AuthController::class, 'create_account']);
    Route::post('user/login',[\App\Http\Controllers\Auth\AuthController::class, 'login']);


    Route::post('user/verify',[\App\Http\Controllers\Auth\AuthController::class, 'verify']);

    Route::get('deposit/narration',[\App\Http\Controllers\User\Deposit\Direct\DirectDepositController::class, 'index']);
    Route::post('deposit/paid',[\App\Http\Controllers\User\Deposit\Direct\DirectDepositController::class, 'store']);

    //investments

    Route::get('deposit/narration',[\App\Http\Controllers\User\Investements\Real_estate\RealEstateInvestMentController::class, 'index']);


});


Route::prefix('v1/investments')->middleware('api') ->group(function () {
    Route::get('/', [\App\Http\Controllers\User\Investements\Real_estate\RealEstateInvestMentController::class, 'index']);
    Route::get('/{investment_id}', [\App\Http\Controllers\User\Investements\Real_estate\RealEstateInvestMentController::class, 'show']);
    
    
    Route::post('/invest', [\App\Http\Controllers\User\Investements\Real_estate\RealEstateInvestMentController::class, 'store']);
    
});

