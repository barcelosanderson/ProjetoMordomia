@extends('layouts.auth')

@section('title', 'Verificar e-mail')

@section('content')
<div class="auth-card">

    <div class="auth-brand">
        <div class="auth-icon">
            <i class="bi bi-envelope-check"></i>
        </div>
        <h1>Verificar e-mail</h1>
        <p>Informe seu e-mail para receber um novo link de verificação.</p>
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

    <form action="{{ route('verification.resend') }}" method="POST">
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
                autofocus
            >

            @error('email')
                <div class="input-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-primary-dark w-100 justify-content-center mt-2">
            <i class="bi bi-send"></i>
            Reenviar link
        </button>
    </form>

    <div class="auth-footer">
        <span>Já verificou?</span>
        <a href="{{ route('login') }}">Voltar ao login</a>
    </div>

</div>
@endsection
