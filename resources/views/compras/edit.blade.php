@extends('layouts.app')

@section('title', 'Editar Item')

@section('content')
<div style="max-width:500px;">
    <div class="mb-4">
        <h1 class="page-title">Editar Item</h1>
        <p class="page-subtitle">Atualize o nome do item</p>
    </div>
    <form action="{{ route('compras.update', $compra->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="label-dark">Nome do item</label>
            <input type="text" name="nome" value="{{ $compra->nome }}"
                   placeholder="Ex: Arroz, Feijão, Leite..."
                   class="input-dark" required autofocus>
        </div>
        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn-primary-dark">Salvar</button>
            <a href="{{ route('compras.index') }}" class="btn-ghost">Cancelar</a>
        </div>
    </form>
</div>
@endsection