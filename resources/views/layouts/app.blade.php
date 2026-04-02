<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mordomia — @yield('title', 'Seu dia organizado')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- CSS customizado -->
    <link href="{{ asset('css/mordomia.css') }}" rel="stylesheet">
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <h1>MordomIA</h1>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('home') }}"
               class="sidebar-link {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="bi bi-chat-dots"></i>
                Chat com IA
            </a>
            <a href="{{ route('tarefas.index') }}"
               class="sidebar-link {{ request()->routeIs('tarefas.*') ? 'active' : '' }}">
                <i class="bi bi-check2-square"></i>
                Tarefas
            </a>
            <a href="{{ route('compromissos.index') }}"
               class="sidebar-link {{ request()->routeIs('compromissos.*') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i>
                Compromissos
            </a>
            <a href="{{ route('financas.index') }}"
               class="sidebar-link {{ request()->routeIs('financas.*') ? 'active' : '' }}">
                <i class="bi bi-cash-coin"></i>
                Finanças
            </a>
            <a href="{{ route('compras.index') }}"
               class="sidebar-link {{ request()->routeIs('compras.*') ? 'active' : '' }}">
                <i class="bi bi-cart3"></i>
                Compras
            </a>
        </nav>
    </aside>

    <!-- Mobile header -->
    <div class="mobile-header">
        <span>Mordomia</span>
    </div>

    <!-- Conteúdo principal -->
    <div class="main-wrapper">
        <div class="p-4">
            @if(session('success'))
                <div class="alert-dark-success">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @yield('scripts')
</body>
</html>