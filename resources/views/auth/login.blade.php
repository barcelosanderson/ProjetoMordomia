@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="auth-card">

    <div class="auth-brand">
        <div class="auth-icon">
            <i class="bi bi-stars"></i>
        </div>
        <h1>MordomIA</h1>
        <p>Entre para organizar sua rotina pessoal.</p>
    </div>

    @if(session('success'))
    <div class="alert-dark-success mb-3">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert-dark-danger mb-3">
        <i class="bi bi-exclamation-circle me-2"></i>
        Verifique os dados informados.
    </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="email" class="label-dark">E-mail</label>
            <input
                type="email"
                id="email"
                name="email"
                class="input-dark @error('email') is-invalid-dark @enderror"
                value="{{ old('email') }}"
                placeholder="seuemail@exemplo.com"
                autocomplete="email"
                autofocus>

            @error('email')
            <div class="input-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="label-dark">Senha</label>
            <input
                type="password"
                id="password"
                name="password"
                class="input-dark @error('password') is-invalid-dark @enderror"
                placeholder="Digite sua senha"
                autocomplete="current-password">

            @error('password')
            <div class="input-error">{{ $message }}</div>
            @enderror
        </div>
        <div class="text-end mb-3">
            <a href="{{ route('password.request') }}" class="auth-small-link">
                Esqueci minha senha
            </a>

            <button type="submit" class="btn-primary-dark w-100 justify-content-center mt-2">
                <i class="bi bi-box-arrow-in-right"></i>
                Entrar
            </button>
    </form>

    <div class="auth-footer">
        <span>Ainda não tem conta?</span>
        <a href="{{ route('register') }}">Criar conta</a>
    </div>

</div>
@endsection
