@extends('layouts.app')

@section('title', 'Nova Tarefa')

@section('content')
<div style="max-width:500px;">
    <div class="mb-4">
        <h1 class="page-title">Nova Tarefa</h1>
        <p class="page-subtitle">Adicione uma nova tarefa à sua lista</p>
    </div>
    <form action="{{ route('tarefas.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="label-dark">Nome da tarefa</label>
            <input type="text" name="nome" placeholder="Ex: Lavar a louça, Fazer compras..."
                   class="input-dark" required autofocus>
            @error('nome')
                <small style="color:#ff6b6b;">{{ $message }}</small>
            @enderror
        </div>
        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn-primary-dark">Adicionar</button>
            <a href="{{ route('tarefas.index') }}" class="btn-ghost">Cancelar</a>
        </div>
    </form>
</div>
@endsection