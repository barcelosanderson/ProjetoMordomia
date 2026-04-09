@extends('layouts.app')

@section('title', 'Nova Transação')

@section('content')
<div style="max-width:500px;">
    <div class="mb-4">
        <h1 class="page-title">Nova Transação</h1>
        <p class="page-subtitle">Registre uma receita ou despesa</p>
    </div>
    <form action="{{ route('financas.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="label-dark">Descrição</label>
            <input type="text" name="nome" placeholder="Ex: Salário, Aluguel, Supermercado..."
                   class="input-dark" required autofocus>
        </div>
        <div class="mb-3">
            <label class="label-dark">Valor (R$)</label>
            <input type="number" name="valor" step="0.01" min="0" placeholder="0,00"
                   class="input-dark" required>
        </div>
        <div class="mb-3">
            <label class="label-dark">Tipo</label>
            <div class="type-toggle">
                <input type="radio" name="tipo" id="tipo_receita" value="receita">
                <label for="tipo_receita">
                    <i class="bi bi-arrow-up-circle me-1"></i> Entrada
                </label>
                <input type="radio" name="tipo" id="tipo_despesa" value="despesa" checked>
                <label for="tipo_despesa">
                    <i class="bi bi-arrow-down-circle me-1"></i> Saída
                </label>
            </div>
        </div>
        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn-primary-dark">Adicionar</button>
            <a href="{{ route('financas.index') }}" class="btn-ghost">Cancelar</a>
        </div>
    </form>
</div>
@endsection