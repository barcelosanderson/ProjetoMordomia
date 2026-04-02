@extends('layouts.app')

@section('title', 'Início')

@section('content')
<div class="d-flex flex-column align-items-center justify-content-center" style="min-height:80vh;">

    <div class="icon-box icon-box-primary mb-4" style="width:4rem;height:4rem;border-radius:1rem;font-size:1.75rem;">
        <i class="bi bi-stars"></i>
    </div>

    <h2 class="fw-bold text-white mb-1">Olá! Sou a MordomIA</h2>
    <p class="page-subtitle mb-4 text-center" style="max-width:360px;">
        Use o menu lateral para gerenciar suas tarefas, compromissos, finanças e lista de compras.
    </p>

    <div class="d-flex flex-wrap gap-2 justify-content-center">
        <a href="{{ route('tarefas.index') }}" class="quick-link">
            <i class="bi bi-check2-square me-1"></i> Ver tarefas
        </a>
        <a href="{{ route('compromissos.index') }}" class="quick-link">
            <i class="bi bi-calendar3 me-1"></i> Ver compromissos
        </a>
        <a href="{{ route('financas.index') }}" class="quick-link">
            <i class="bi bi-cash-coin me-1"></i> Ver finanças
        </a>
        <a href="{{ route('compras.index') }}" class="quick-link">
            <i class="bi bi-cart3 me-1"></i> Lista de compras
        </a>
    </div>

</div>
@endsection