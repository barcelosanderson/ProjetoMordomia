@extends('layouts.app')

@section('title', 'Compromissos')

@section('content')

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="page-title">Compromissos</h1>
            <p class="page-subtitle">Sua agenda organizada</p>
        </div>
        <a href="{{ route('compromissos.create') }}" class="btn-primary-dark">
            <i class="bi bi-plus-lg"></i> Novo
        </a>
    </div>

    @php $agrupados = $compromissos->groupBy('data'); @endphp

    @forelse($agrupados->sortKeys() as $data => $grupo)
        <p class="section-title mt-3">
            {{ \Carbon\Carbon::parse($data)->locale('pt_BR')->isoFormat('dddd, D [de] MMMM') }}
        </p>
        @foreach($grupo->sortBy('hora') as $compromisso)
        <div class="list-item">
            <div class="icon-box icon-box-primary">
                <i class="bi bi-clock"></i>
            </div>
            <div class="flex-grow-1">
                <div class="item-text">{{ $compromisso->titulo }}</div>
                <div class="page-subtitle" style="font-size:.75rem;">
                    {{ \Carbon\Carbon::parse($compromisso->hora)->format('H:i') }}
                </div>
            </div>
            <div class="item-actions">
                <a href="{{ route('compromissos.edit', $compromisso->id) }}" class="btn-icon" title="Editar">
                    <i class="bi bi-pencil"></i>
                </a>
                <form action="{{ route('compromissos.destroy', $compromisso->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-icon btn-icon-danger" title="Excluir">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    @empty
        <p class="text-center page-subtitle py-5">Nenhum compromisso. Adicione um novo!</p>
    @endforelse

@endsection