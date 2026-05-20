@extends('layouts.app')

@section('title', 'Início')

@section('content')
<div class="home-wrapper {{ count($historico) === 0 ? 'sem-historico' : '' }}">

    {{-- Histórico do chat --}}
    @if(count($historico) > 0)
    <div class="chat-historico" id="chatHistorico">

        <div class="chat-historico-header">
            <form action="{{ route('chat.limpar') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="chat-limpar-btn">
                    <i class="bi bi-trash3 me-1"></i> Limpar conversa
                </button>
            </form>
        </div>

        @foreach($historico as $item)
            <div class="chat-balao chat-balao-usuario">
                <span class="chat-balao-label">Você</span>
                <p>{{ $item['pergunta'] }}</p>
            </div>

            <div class="chat-balao chat-balao-ia">
                <span class="chat-balao-label">
                    <i class="bi bi-stars me-1"></i> MordomIA
                </span>
                <p>{!! nl2br(e($item['resposta'])) !!}</p>
            </div>
        @endforeach

    </div>

    @else
    {{-- Estado inicial sem histórico --}}
    <div id="chatVazio" class="d-flex flex-column align-items-center">
        <div class="icon-box icon-box-primary mb-4">
            <i class="bi bi-stars"></i>
        </div>
        <h2 class="fw-bold text-white mb-1">Olá! Sou a MordomIA</h2>
        <p class="page-subtitle mb-4 text-center">
            Use o menu lateral para gerenciar suas tarefas, compromissos, finanças e lista de compras.
        </p>
        <div class="d-flex flex-wrap gap-2 justify-content-center mb-5">
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
    @endif

    @if(session('erro_chat'))
        <div class="alert alert-danger mt-3">{{ session('erro_chat') }}</div>
    @endif

    {{-- Barra de chat --}}
    <div class="chat-input-wrapper">
        <form class="chat-input-form" id="chatForm" action="{{ route('chat.enviar') }}" method="POST">
            @csrf
            <div class="chat-input-box">
                <textarea
                    id="chatInput"
                    name="mensagem"
                    class="chat-textarea"
                    placeholder="Pergunte algo à MordomIA…"
                    rows="1"
                    maxlength="2000"
                    autocomplete="off"
                ></textarea>
                <div class="chat-input-actions">
                    <button type="submit" class="chat-send-btn" id="sendBtn" disabled>
                        <i class="bi bi-arrow-up"></i>
                    </button>
                </div>
            </div>
        </form>
        <p class="chat-hint">A MordomIA pode cometer erros. Verifique informações importantes.</p>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    const textarea = document.getElementById('chatInput');
    const sendBtn  = document.getElementById('sendBtn');

    function sync() {
        const len = textarea.value.trim().length;
        sendBtn.disabled = len === 0;
        textarea.style.height = 'auto';
        textarea.style.height = Math.min(textarea.scrollHeight, 160) + 'px';
    }

    sync();
    textarea.addEventListener('input', sync);

    textarea.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (textarea.value.trim().length > 0) {
            document.getElementById('chatForm').requestSubmit();
            }
        }
    });

    // Rola até o fim quando há histórico
    const historico = document.getElementById('chatHistorico');
    if (historico) {
        window.scrollTo({ top: document.body.scrollHeight, behavior: 'instant' });
    }
})();
</script>
@endpush