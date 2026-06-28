<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UrlController;

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

Auth::routes();

Route::get('/s/{code}', [UrlController::class, 'redirect'])->name('url.redirect');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/urls', [UrlController::class, 'index'])->name('urls.index');
    Route::post('/urls', [UrlController::class, 'store'])->name('urls.store');
    Route::delete('/urls/{id}', [UrlController::class, 'destroy'])->name('urls.destroy');
});