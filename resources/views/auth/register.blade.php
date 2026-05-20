@extends('layouts.auth')

@section('title', 'Criar conta')

@section('content')
<div class="auth-card auth-card-large">

    <div class="auth-brand">
        <div class="auth-icon">
            <i class="bi bi-person-plus"></i>
        </div>
        <h1>Criar conta</h1>
        <p>Cadastre-se para usar sua MordomIA pessoal.</p>
    </div>

    @if($errors->any())
        <div class="alert-dark-danger mb-3">
            <i class="bi bi-exclamation-circle me-2"></i>
            Verifique os dados informados.
        </div>
    @endif

    <form action="{{ route('register.post') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="label-dark">Nome</label>
            <input
                type="text"
                id="name"
                name="name"
                class="input-dark @error('name') is-invalid-dark @enderror"
                value="{{ old('name') }}"
                placeholder="Seu nome"
                autocomplete="name"
                autofocus
            >

            @error('name')
                <div class="input-error">{{ $message }}</div>
            @enderror
        </div>

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
            >

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
                placeholder="Mínimo 8 caracteres, letras e números"
                autocomplete="new-password"
            >

            @error('password')
                <div class="input-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="label-dark">Confirmar senha</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="input-dark"
                placeholder="Digite a senha novamente"
                autocomplete="new-password"
            >
        </div>

        <button type="submit" class="btn-primary-dark w-100 justify-content-center mt-2">
            <i class="bi bi-person-check"></i>
            Criar conta
        </button>
    </form>

    <div class="auth-footer">
        <span>Já tem conta?</span>
        <a href="{{ route('login') }}">Entrar</a>
    </div>

</div>
@endsection
