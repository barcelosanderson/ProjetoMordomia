@extends('layouts.app')

@section('title', 'Novo Item')

@section('content')
<div style="max-width:500px;">
    <div class="mb-4">
        <h1 class="page-title">Novo Item</h1>
        <p class="page-subtitle">Adicione um item à lista de compras</p>
    </div>
    <form action="{{ route('compras.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="label-dark">Nome do item</label>
            <input type="text" name="nome" placeholder="Ex: Arroz, Feijão, Leite..."
                   class="input-dark" required autofocus>
        </div>
        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn-primary-dark">Adicionar</button>
            <a href="{{ route('compras.index') }}" class="btn-ghost">Cancelar</a>
        </div>
    </form>
</div>
@endsection