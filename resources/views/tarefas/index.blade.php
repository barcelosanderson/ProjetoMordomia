@extends('layouts.app')

@section('title', 'Tarefas')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="page-title">Tarefas</h1>
            <p class="page-subtitle">Organize suas tarefas domésticas por dia da semana</p>
        </div>

        <a href="{{ route('tarefas.create') }}" class="btn-primary-dark">
            <i class="bi bi-plus-lg"></i> Nova Tarefa
        </a>
    </div>

    {{-- Cards dos dias da semana --}}
    <div class="week-board mb-4">
        @foreach($cardsDias as $dia)
            <a href="{{ route('tarefas.index', ['dia' => $dia['numero']]) }}"
               class="weekday-card {{ $dia['selecionado'] ? 'active' : '' }} {{ $dia['hoje'] ? 'today' : '' }}">

                <div class="weekday-card-header">
                    <span class="weekday-name">{{ $dia['nome'] }}</span>

                    @if($dia['hoje'])
                        <span class="weekday-today-badge">Hoje</span>
                    @endif
                </div>

                <div class="weekday-numbers">
                    <strong>{{ $dia['total'] }}</strong>
                    <span>tarefas</span>
                </div>

                <div class="weekday-status">
                    <span>{{ $dia['concluidas'] }} feitas</span>
                    <span>{{ $dia['pendentes'] }} pendentes</span>
                </div>

                <div class="weekday-progress">
                    <div class="weekday-progress-fill" style="width: {{ $dia['percentual'] }}%;"></div>
                </div>
            </a>
        @endforeach
    </div>

    <p class="section-title">
        {{ $cardsDias[$diaSelecionado]['nome'] ?? 'Dia' }}
        @if($cardsDias[$diaSelecionado]['hoje'] ?? false)
            <span style="color: var(--color-primary);">• Hoje</span>
        @endif
    </p>

    @php
        $pendentes = $tarefas->where('concluida_no_dia', false);
        $concluidas = $tarefas->where('concluida_no_dia', true);
    @endphp

    @if($pendentes->count() > 0)
        <p class="section-title">Pendentes ({{ $pendentes->count() }})</p>

        @foreach($pendentes as $tarefa)
            <div class="list-item">
                <form action="{{ route('tarefas.toggle', $tarefa->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="data_referencia" value="{{ $dataSelecionada }}">
                    <input type="hidden" name="dia_semana" value="{{ $diaSelecionado }}">

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

    @if($concluidas->count() > 0)
        <p class="section-title mt-4">Concluídas ({{ $concluidas->count() }})</p>

        @foreach($concluidas as $tarefa)
            <div class="list-item list-item-muted">
                <form action="{{ route('tarefas.toggle', $tarefa->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="data_referencia" value="{{ $dataSelecionada }}">
                    <input type="hidden" name="dia_semana" value="{{ $diaSelecionado }}">

                    <button type="submit" class="item-checkbox checked" title="Desmarcar">
                        <i class="bi bi-check" style="color:#22c55e;font-size:.75rem;"></i>
                    </button>
                </form>

                <span class="item-text item-text-done">{{ $tarefa->nome }}</span>

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

    @if($tarefas->isEmpty())
        <div class="weekday-empty-state">
            <div class="icon-box icon-box-primary mb-3">
                <i class="bi bi-calendar-check"></i>
            </div>
            <p class="text-center page-subtitle">
                Nenhuma tarefa para este dia da semana.
            </p>
        </div>
    @endif

@endsection
