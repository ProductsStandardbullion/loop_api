<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('',[\App\Http\Controllers\User\Auth\AuthController::class, 'index'])->name('index');
Route::get('signup',[\App\Http\Controllers\User\Auth\AuthController::class, 'register'])->name('index.register');
Route::post('signup',[\App\Http\Controllers\User\Auth\AuthController::class, 'reg'])->name('index.register.post');


Route::prefix('hq/')->group(function () {
    Route::get('',[\App\Http\Controllers\Admin\Auth\AuthController::class,'index']);
    Route::post('login',[\App\Http\Controllers\Admin\Auth\AuthController::class,'login'])->name('login');
});


Route::prefix('hq/dashboard')->middleware(['auth','isAdmin']) ->group(function () {
    Route::get('', [\App\Http\Controllers\Admin\hq\dashboard\DashboardController::class, 'index'])->name('hq');
    Route::get('investments/realestate', [\App\Http\Controllers\Admin\hq\Investments\RealestateInvestmentController::class, 'index'])->name('hq.investment.realestate');
    Route::post('investments/realestate/store', [\App\Http\Controllers\Admin\hq\Investments\RealestateInvestmentController::class, 'store'])->name('hq.investment.realestate.store');

    Route::get('investments/realestate/{investment_id}', [\App\Http\Controllers\Admin\hq\Investments\RealestateInvestmentController::class, 'show'])->name('hq.investment.realestate.show');

    Route::get('investments/realestate/{id}', [\App\Http\Controllers\Admin\hq\Investments\RealestateInvestmentController::class, 'destroy'])->name('hq.investment.realestate.destroy');

});


Route::prefix('verify/account/')->group(function () {
Route::get('402e2fc8-b270-45f3-91dc-54e23aa69454/{id}',[\App\Http\Controllers\Auth\AuthController::class, 'verify'])->name('verify.account');
});







