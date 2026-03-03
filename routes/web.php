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

Route::middleware(['auth', 'check.recruta'])->group(function () {
        // NOVA ROTA PARA A BUSCA EM TEMPO REAL
    Route::get('/dashboard/search-operator', [HomeController::class, 'searchOperator'])->name('dashboard.searchOperator');

    // ROTAS DE TREINAMENTO (Para Guias/Instrutores - Validação dinâmica no Controller)
    Route::get('/treinamentos/aplicar', [App\Http\Controllers\TreinamentoController::class, 'create'])->name('treinamentos.create');
    Route::post('/treinamentos/aplicar', [App\Http\Controllers\TreinamentoController::class, 'store'])->name('treinamentos.store');

    // PERFIL PÚBLICO DO OPERADOR
    Route::get('/perfil/{user}', [App\Http\Controllers\PerfilController::class, 'show'])->name('perfil.show');
});

use App\Http\Controllers\Admin\OperadorController;
use App\Http\Controllers\Admin\ComunicadoController;
use App\Http\Controllers\Admin\AulaController;
use App\Http\Controllers\Admin\FuncaoAssignmentController;
use App\Http\Controllers\Admin\PromocaoController;
use App\Http\Controllers\Admin\VendaCargoController;

Route::middleware(['auth', 'check.recruta'])->group(function () {
    // ROTA PARA LÍDERES ATRIBUÍREM CARGOS (Validação feita dentro do controller)
    Route::get('/atribuir-cargos', [FuncaoAssignmentController::class, 'index'])->name('admin.funcoes.assign.index');
    Route::post('/atribuir-cargos', [FuncaoAssignmentController::class, 'store'])->name('admin.funcoes.assign.store');
    Route::delete('/atribuir-cargos/{user}/revogar', [FuncaoAssignmentController::class, 'destroy'])->name('admin.funcoes.assign.destroy');

    // ROTA PARA PROMOÇÕES, REBAIXAMENTOS E EXONERAÇÕES DE PATENTE
    Route::get('/promover-efetivo', [PromocaoController::class, 'index'])->name('admin.promocoes.index');
    Route::post('/promover-efetivo', [PromocaoController::class, 'store'])->name('admin.promocoes.store');
    Route::post('/rebaixar-efetivo', [PromocaoController::class, 'demote'])->name('admin.promocoes.demote');
    Route::post('/exonerar-efetivo', [PromocaoController::class, 'exonerate'])->name('admin.promocoes.exonerate');
});

Route::get('/test-dump', function() {
    return [
        'aulas' => \App\Models\Aula::with('roles')->get()->map(fn($a) => ['id' => $a->id, 'roles' => $a->roles->pluck('id')]),
        'alunos' => \App\Models\User::where('nickname', 'like', '%')->with('roles')->limit(5)->get()->map(fn($u) => ['id' => $u->id, 'nickname' => $u->nickname, 'roles' => $u->roles->pluck('id', 'name')])
    ];
});

Route::middleware(['auth', 'superadmin'])->prefix('admin')->name('admin.')->group(function () {
    // Rota para gerenciar Patentes (Roles com Hierarquia)
    Route::resource('patentes', PatenteController::class)->except(['show']); // Mudei de 'roles' para 'patentes' para clareza
    Route::resource('permissoes', PermissionController::class)
        ->parameters(['permissoes' => 'permission'])
        ->only(['index', 'store', 'destroy']);
    Route::post('permissoes/{permission}/reset', [PermissionController::class, 'reset'])->name('permissoes.reset');

    // ROTA NOVA para gerenciar Funções (Roles sem Hierarquia)
    Route::resource('funcoes', FunctionRoleController::class)->except(['show']);

    // ROTA NOVA para gerenciar Operadores (Users)
    Route::resource('operadores', OperadorController::class)->except(['show']);

    // ROTA NOVA para gerenciar Comunicados
    Route::resource('comunicados', ComunicadoController::class)->except(['show']);

    // ROTA NOVA para gerenciar Formações (Aula Types)
    Route::resource('aulas', AulaController::class)->except(['show']);

    Route::middleware(['permission:vender_cargos'])->group(function () {
        Route::get('venda-cargos', [VendaCargoController::class, 'index'])->name('vendas.index');
        Route::post('venda-cargos', [VendaCargoController::class, 'store'])->name('vendas.store');
    });
});


use App\Http\Controllers\ScriptController;

Route::middleware(['auth', 'check.recruta'])->group(function () {
    Route::get('/scripts', [ScriptController::class, 'index'])->name('scripts.index');
    Route::post('/scripts/categories', [ScriptController::class, 'storeCategory'])->name('scripts.categories.store');
    Route::post('/scripts', [ScriptController::class, 'storeScript'])->name('scripts.store');
    Route::put('/scripts/{script}', [ScriptController::class, 'updateScript'])->name('scripts.update');
    Route::delete('/scripts/{script}', [ScriptController::class, 'destroyScript'])->name('scripts.destroy');
});

Route::get('/', [HomeController::class, 'index'])->name('dashboard')->middleware(Authenticate::class);
