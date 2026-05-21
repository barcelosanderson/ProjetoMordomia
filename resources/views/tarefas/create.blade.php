@extends('layouts.app')

@section('title', 'Nova Tarefa')

@section('content')
<div style="max-width:560px;">
    <div class="mb-4">
        <h1 class="page-title">Nova Tarefa</h1>
        <p class="page-subtitle">Adicione uma tarefa e selecione em quais dias ela se repete</p>
    </div>

    <form action="{{ route('tarefas.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="label-dark">Nome da tarefa</label>
            <input
                type="text"
                name="nome"
                value="{{ old('nome') }}"
                placeholder="Ex: Lavar a louça, Fazer compras..."
                class="input-dark"
                required
                autofocus
            >

            @error('nome')
                <small style="color:#ff6b6b;">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label class="label-dark">Dias da semana</label>

            <div class="day-selector-grid">
                @foreach($diasSemana as $numero => $nome)
                    @php
                        $diasSelecionados = array_map('intval', old('dias_semana', []));
                    @endphp

                    <label class="day-selector-item">
                        <input
                            type="checkbox"
                            name="dias_semana[]"
                            value="{{ $numero }}"
                            {{ in_array($numero, $diasSelecionados) ? 'checked' : '' }}
                        >
                        <span>{{ $nome }}</span>
                    </label>
                @endforeach
            </div>

            @error('dias_semana')
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
