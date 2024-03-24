<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Artisan;
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

Route::get('/clear', function () {
	Artisan::call('cache:clear');
	Artisan::call('config:clear');
	Artisan::call('config:cache');
	Artisan::call('view:clear');
	Artisan::call('route:clear');
	return "Cleared!";
});

Route::prefix('login')->group(function () {
    Route::get('/', [LoginController::class, 'index'])->name('login.index');
    Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('login.authenticate');
    Route::post('/register', [LoginController::class, 'register'])->name('login.register');
    Route::get('/{nickname}', [LoginController::class, 'show'])->name('login.show');
});

Route::get('/perfil', [LoginController::class, 'perfil']);




Route::get('/', [HomeController::class, 'index'])->name('dashboard')->middleware(Authenticate::class);
