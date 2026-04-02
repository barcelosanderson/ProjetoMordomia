@extends('layouts.app')

@section('title', 'Lista de Compras')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="page-title">Lista de Compras</h1>
            <p class="page-subtitle">
                {{ $compras->where('comprado', true)->count() }} de {{ $compras->count() }} itens comprados
            </p>
        </div>
        <a href="{{ route('compras.create') }}" class="btn-primary-dark">
            <i class="bi bi-plus-lg"></i> Novo Item
        </a>
    </div>

    @if($compras->count() > 0)
        @php $progresso = round(($compras->where('comprado', true)->count() / $compras->count()) * 100); @endphp
        <div class="card-dark mb-4">
            <div class="d-flex justify-content-between mb-2" style="font-size:.75rem;color:#888;">
                <span>Progresso</span>
                <span>{{ $progresso }}%</span>
            </div>
            <div class="progress-dark">
                <div class="progress-fill" style="width:{{ $progresso }}%"></div>
            </div>
        </div>
    @endif

    @php $pendentes = $compras->where('comprado', false); @endphp
    @if($pendentes->count() > 0)
        <p class="section-title">A comprar ({{ $pendentes->count() }})</p>
        @foreach($pendentes as $compra)
        <div class="list-item">
            <form action="{{ route('compras.toggle', $compra->id) }}" method="POST">
                @csrf
                <button type="submit" class="item-checkbox" title="Marcar como comprado"></button>
            </form>
            <span class="item-text">{{ $compra->nome }}</span>
            <div class="item-actions">
                <a href="{{ route('compras.edit', $compra->id) }}" class="btn-icon" title="Editar">
                    <i class="bi bi-pencil"></i>
                </a>
                <form action="{{ route('compras.destroy', $compra->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-icon btn-icon-danger" title="Excluir">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    @endif

    @php $comprados = $compras->where('comprado', true); @endphp
    @if($comprados->count() > 0)
        <p class="section-title mt-4">Comprados ({{ $comprados->count() }})</p>
        @foreach($comprados as $compra)
        <div class="list-item list-item-muted">
            <form action="{{ route('compras.toggle', $compra->id) }}" method="POST">
                @csrf
                <button type="submit" class="item-checkbox checked-accent" title="Desmarcar">
                    <i class="bi bi-check" style="color:#06b6d4;font-size:.75rem;"></i>
                </button>
            </form>
            <span class="item-text item-text-done">{{ $compra->nome }}</span>
            <form action="{{ route('compras.destroy', $compra->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-icon btn-icon-danger" title="Excluir">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>
        @endforeach
    @endif

    @if($compras->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-cart3" style="font-size:3rem;color:#333;"></i>
            <p class="page-subtitle mt-3">Sua lista está vazia. Adicione itens!</p>
        </div>
    @endif

@endsection