@extends('layouts.app')

@section('title', 'Novo Compromisso')

@section('content')
<div style="max-width:500px;">
    <div class="mb-4">
        <h1 class="page-title">Novo Compromisso</h1>
        <p class="page-subtitle">Adicione um compromisso à sua agenda</p>
    </div>
    <form action="{{ route('compromissos.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="label-dark">Título</label>
            <input type="text" name="titulo" placeholder="Ex: Reunião, Consulta médica..."
                   class="input-dark" required autofocus>
        </div>
        <div class="row g-3 mb-3">
            <div class="col">
                <label class="label-dark">Data</label>
                <input type="date" name="data" class="input-dark" required>
            </div>
            <div class="col-auto" style="width:140px;">
                <label class="label-dark">Hora</label>
                <input type="time" name="hora" class="input-dark" required>
            </div>
        </div>
        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn-primary-dark">Adicionar</button>
            <a href="{{ route('compromissos.index') }}" class="btn-ghost">Cancelar</a>
        </div>
    </form>
</div>
@endsection