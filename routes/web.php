<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TarefaController;
use App\Http\Controllers\CompromissoController;
use App\Http\Controllers\TransacaoController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ChatController;

//Home — Chat com IA
Route::get('/', [HomeController::class, 'index'])->name('home'); // chama o método index do HomeController e nomeia como home

//Tarefas
Route::resource('tarefas', TarefaController::class); // cria 7 rotas (listar, criar, salvar, exibir, editar, att e excluir) 
Route::post('tarefas/{id}/toggle', [TarefaController::class, 'toggle'])->name('tarefas.toggle'); // toggle - muda o estado, tipo true/false

//Compromissos
Route::resource('compromissos', CompromissoController::class);

//Finanças
Route::get('financas', [TransacaoController::class, 'index'])->name('financas.index');
Route::get('financas/create', [TransacaoController::class, 'create'])->name('financas.create');
Route::post('financas', [TransacaoController::class, 'store'])->name('financas.store');
Route::delete('financas/{id}', [TransacaoController::class, 'destroy'])->name('financas.destroy');

//Compras
Route::resource('compras', CompraController::class);
Route::post('compras/{id}/toggle', [CompraController::class, 'toggle'])->name('compras.toggle');

// Chat IA
Route::post('chat/enviar', [ChatController::class, 'enviar'])->name('chat.enviar');
Route::post('chat/limpar', [ChatController::class, 'limpar'])->name('chat.limpar');