@extends('layouts.app')

@section('title', 'Tarefas')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="page-title">Tarefas</h1>
            <p class="page-subtitle">Organize suas tarefas domésticas</p>
        </div>
        <a href="{{ route('tarefas.create') }}" class="btn-primary-dark">
            <i class="bi bi-plus-lg"></i> Nova Tarefa
        </a>
    </div>

    @php $pendentes = $tarefas->where('concluida', false); @endphp
    @if($pendentes->count() > 0)
        <p class="section-title">Pendentes ({{ $pendentes->count() }})</p>
        @foreach($pendentes as $tarefa)
        <div class="list-item">
            <form action="{{ route('tarefas.toggle', $tarefa->id) }}" method="POST">
                @csrf
                <button type="submit" class="item-checkbox" title="Marcar como concluída"></button>
            </form>
            <span class="item-text">{{ $tarefa->nome }}</span>
            <div class="item-actions">
                <a href="{{ route('tarefas.edit', $tarefa->id) }}" class="btn-icon" title="Editar">
                    <i class="bi bi-pencil"></i>
                </a>
                <form action="{{ route('tarefas.destroy', $tarefa->id) }}" method="POST">
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

    @php $concluidas = $tarefas->where('concluida', true); @endphp
    @if($concluidas->count() > 0)
        <p class="section-title mt-4">Concluídas ({{ $concluidas->count() }})</p>
        @foreach($concluidas as $tarefa)
        <div class="list-item list-item-muted">
            <form action="{{ route('tarefas.toggle', $tarefa->id) }}" method="POST">
                @csrf
                <button type="submit" class="item-checkbox checked" title="Desmarcar">
                    <i class="bi bi-check" style="color:#22c55e;font-size:.75rem;"></i>
                </button>
            </form>
            <span class="item-text item-text-done">{{ $tarefa->nome }}</span>
            <form action="{{ route('tarefas.destroy', $tarefa->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-icon btn-icon-danger" title="Excluir">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>
        @endforeach
    @endif

    @if($tarefas->isEmpty())
        <p class="text-center page-subtitle py-5">Nenhuma tarefa. Adicione uma nova!</p>
    @endif

@endsection