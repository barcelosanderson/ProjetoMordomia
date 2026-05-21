@extends('layouts.auth')

@section('title', 'Nova senha')

@section('content')
<div class="auth-card auth-card-large">

    <div class="auth-brand">
        <div class="auth-icon">
            <i class="bi bi-shield-lock"></i>
        </div>
        <h1>Nova senha</h1>
        <p>Defina uma nova senha para acessar sua conta.</p>
    </div>

    @if($errors->any())
        <div class="alert-dark-danger mb-3">
            <i class="bi bi-exclamation-circle me-2"></i>
            Verifique os dados informados.
        </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            <label for="email" class="label-dark">E-mail</label>
            <input
                type="email"
                id="email"
                name="email"
                class="input-dark @error('email') is-invalid-dark @enderror"
                value="{{ old('email', $email) }}"
                placeholder="seuemail@exemplo.com"
                autocomplete="email"
            >

            @error('email')
                <div class="input-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="label-dark">Nova senha</label>
            <input
                type="password"
                id="password"
                name="password"
                class="input-dark @error('password') is-invalid-dark @enderror"
                placeholder="Mínimo 8 caracteres, letras e números"
                autocomplete="new-password"
                autofocus
            >

            @error('password')
                <div class="input-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="label-dark">Confirmar nova senha</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="input-dark"
                placeholder="Digite a nova senha novamente"
                autocomplete="new-password"
            >
        </div>

        <button type="submit" class="btn-primary-dark w-100 justify-content-center mt-2">
            <i class="bi bi-check2-circle"></i>
            Redefinir senha
        </button>
    </form>

    <div class="auth-footer">
        <span>Não quer alterar?</span>
        <a href="{{ route('login') }}">Voltar ao login</a>
    </div>

</div>
@endsection
