<?php

use App\Http\Controllers\Admin\FunctionRoleController;
use App\Http\Controllers\Admin\PatenteController;
use App\Http\Controllers\Admin\PermissionController;
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
    Route::post('/recover', [LoginController::class, 'recover'])->name('login.recover');
    Route::get('/{nickname}', [LoginController::class, 'show'])->name('login.show');
});

Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/perfil', [LoginController::class, 'perfil']);

Route::middleware('auth')->group(function () {
    // NOVA ROTA PARA A BUSCA EM TEMPO REAL
    Route::get('/dashboard/search-operator', [HomeController::class, 'searchOperator'])->name('dashboard.searchOperator');
});

Route::middleware(['auth', 'superadmin'])->prefix('admin')->name('admin.')->group(function () {
    // Rota para gerenciar Patentes (Roles com Hierarquia)
    Route::resource('patentes', PatenteController::class)->except(['show']); // Mudei de 'roles' para 'patentes' para clareza
    Route::resource('permissoes', PermissionController::class)
        ->parameters(['permissoes' => 'permission'])
        ->only(['index', 'store', 'destroy']);

    // ROTA NOVA para gerenciar Funções (Roles sem Hierarquia)
    Route::resource('funcoes', FunctionRoleController::class)->except(['show']);
});






Route::get('/', [HomeController::class, 'index'])->name('dashboard')->middleware(Authenticate::class);
