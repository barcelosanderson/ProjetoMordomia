@extends('layouts.app')

@section('title', 'Resposta da MordomIA')

@section('content')
<div class="d-flex flex-column align-items-center justify-content-center" style="min-height:80vh;">

    <div class="chat-resposta-card">

        {{-- Pergunta do usuário --}}
        <div class="chat-balao chat-balao-usuario">
            <span class="chat-balao-label">Você</span>
            <p>{{ $pergunta }}</p>
        </div>

        {{-- Resposta da IA --}}
        <div class="chat-balao chat-balao-ia">
            <span class="chat-balao-label">
                <i class="bi bi-stars me-1"></i> MordomIA
            </span>
            <p>{!! nl2br(e($resposta)) !!}</p>
        </div>

        {{-- Voltar --}}
        <div class="text-center mt-4">
            <a href="{{ route('home') }}" class="quick-link">
                <i class="bi bi-arrow-left me-1"></i> Nova pergunta
            </a>
        </div>

    </div>

</div>
@endsection