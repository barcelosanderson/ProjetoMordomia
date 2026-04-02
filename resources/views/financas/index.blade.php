@extends('layouts.app')

@section('title', 'Finanças')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="page-title">Finanças</h1>
            <p class="page-subtitle">Controle suas receitas e despesas</p>
        </div>
        <a href="{{ route('financas.create') }}" class="btn-primary-dark">
            <i class="bi bi-plus-lg"></i> Nova
        </a>
    </div>

    <!-- Cards de resumo -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-4">
            <div class="summary-card">
                <p class="summary-label">Saldo</p>
                <p class="summary-value" style="color: {{ $saldo >= 0 ? '#22c55e' : '#ef4444' }};">
                    R$ {{ number_format($saldo, 2, ',', '.') }}
                </p>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="summary-card">
                <p class="summary-label" style="color:#22c55e;">
                    <i class="bi bi-graph-up-arrow me-1"></i> Receitas
                </p>
                <p class="summary-value text-white">R$ {{ number_format($totalReceitas, 2, ',', '.') }}</p>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="summary-card">
                <p class="summary-label" style="color:#ef4444;">
                    <i class="bi bi-graph-down-arrow me-1"></i> Despesas
                </p>
                <p class="summary-value text-white">R$ {{ number_format($totalDespesas, 2, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Histórico -->
    <p class="section-title">Histórico</p>

    @forelse($transacoes as $transacao)
    <div class="list-item">
        <div class="icon-box {{ $transacao->tipo === 'receita' ? 'icon-box-success' : 'icon-box-danger' }}">
            <i class="bi {{ $transacao->tipo === 'receita' ? 'bi-graph-up-arrow' : 'bi-graph-down-arrow' }}"></i>
        </div>
        <div class="flex-grow-1">
            <div class="item-text">{{ $transacao->nome }}</div>
            <div class="page-subtitle" style="font-size:.75rem;">
                {{ $transacao->tipo === 'receita' ? 'Entrada' : 'Saída' }}
            </div>
        </div>
        <span class="fw-semibold me-2"
              style="color:{{ $transacao->tipo === 'receita' ? '#22c55e' : '#ef4444' }};font-size:.875rem;">
            {{ $transacao->tipo === 'receita' ? '+' : '-' }}R$ {{ number_format($transacao->valor, 2, ',', '.') }}
        </span>
        <form action="{{ route('financas.destroy', $transacao->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-icon btn-icon-danger" title="Excluir">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    </div>
    @empty
        <p class="text-center page-subtitle py-5">Nenhuma transação registrada.</p>
    @endforelse

@endsection