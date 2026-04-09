@extends('layouts.app')

@section('title', 'Editar Compromisso')

@section('content')
<div style="max-width:500px;">
    <div class="mb-4">
        <h1 class="page-title">Editar Compromisso</h1>
        <p class="page-subtitle">Atualize os dados do compromisso</p>
    </div>
    <form action="{{ route('compromissos.update', $compromisso->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="label-dark">Título</label>
            <input type="text" name="titulo" value="{{ $compromisso->titulo }}"
                   placeholder="Ex: Reunião, Consulta médica..."
                   class="input-dark" required autofocus>
            @error('titulo')
                <small style="color:#ff6b6b;">{{ $message }}</small>
            @enderror
        </div>
        <div class="row g-3 mb-3">
            <div class="col">
                <label class="label-dark">Data</label>
                <input type="date" name="data" value="{{ $compromisso->data }}"
                       class="input-dark" required>
                @error('data')
                    <small style="color:#ff6b6b;">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-auto" style="width:140px;">
                <label class="label-dark">Hora</label>
                <input type="time" name="hora"
                       value="{{ \Carbon\Carbon::parse($compromisso->hora)->format('H:i') }}"
                       class="input-dark" required>
                @error('hora')
                    <small style="color:#ff6b6b;">{{ $message }}</small>
                @enderror
            </div>
        </div>
        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn-primary-dark">Salvar</button>
            <a href="{{ route('compromissos.index') }}" class="btn-ghost">Cancelar</a>
        </div>
    </form>
</div>
@endsection