<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TarefaController;
use App\Http\Controllers\CompromissoController;
use App\Http\Controllers\TransacaoController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| Rotas públicas
|--------------------------------------------------------------------------
| Essas rotas podem ser acessadas por usuários que ainda não estão logados.
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // Recuperação de senha
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPassword'])
        ->name('password.request');

    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetPassword'])
        ->name('password.reset');

    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
        ->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Rotas protegidas
|--------------------------------------------------------------------------
| Tudo que está aqui exige usuário autenticado.
*/
Route::middleware('auth')->group(function () {

    // Home — Chat com IA
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Tarefas
    Route::resource('tarefas', TarefaController::class);
    Route::post('tarefas/{id}/toggle', [TarefaController::class, 'toggle'])->name('tarefas.toggle');

    // Compromissos
    Route::resource('compromissos', CompromissoController::class);

    // Finanças
    Route::get('financas', [TransacaoController::class, 'index'])->name('financas.index');
    Route::get('financas/create', [TransacaoController::class, 'create'])->name('financas.create');
    Route::post('financas', [TransacaoController::class, 'store'])->name('financas.store');
    Route::delete('financas/{id}', [TransacaoController::class, 'destroy'])->name('financas.destroy');

    // Compras
    Route::resource('compras', CompraController::class);
    Route::post('compras/{id}/toggle', [CompraController::class, 'toggle'])->name('compras.toggle');

    // Chat IA
    Route::post('chat/enviar', [ChatController::class, 'enviar'])->name('chat.enviar');
    Route::post('chat/limpar', [ChatController::class, 'limpar'])->name('chat.limpar');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
