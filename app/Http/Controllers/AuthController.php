<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Exibe a tela de login.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Processa o login do usuário.
     */
    public function login(Request $request)
    {
        // Validação inicial dos campos enviados pelo formulário.
        $credenciais = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Informe seu e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'password.required' => 'Informe sua senha.',
        ]);

        // Tenta autenticar o usuário usando e-mail e senha.
        if (!Auth::attempt($credenciais)) {
            throw ValidationException::withMessages([
                'email' => 'E-mail ou senha inválidos.',
            ]);
        }

        // Regenera a sessão para evitar session fixation.
        $request->session()->regenerate();

        return redirect()
            ->intended(route('home'))
            ->with('success', 'Login realizado com sucesso.');
    }

    /**
     * Exibe a tela de cadastro.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Processa o cadastro do usuário.
     */
    public function register(Request $request)
    {
        // Validação completa do cadastro.
        $dados = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->numbers(),
            ],
        ], [
            'name.required' => 'Informe seu nome.',
            'name.min' => 'O nome deve ter pelo menos 3 caracteres.',
            'name.max' => 'O nome não pode ultrapassar 100 caracteres.',

            'email.required' => 'Informe seu e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'email.max' => 'O e-mail não pode ultrapassar 150 caracteres.',
            'email.unique' => 'Este e-mail já está cadastrado.',

            'password.required' => 'Informe sua senha.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.letters' => 'A senha deve conter pelo menos uma letra.',
            'password.numbers' => 'A senha deve conter pelo menos um número.',
        ]);

        // Cria o usuário no banco.
        $usuario = User::create([
            'name' => $dados['name'],
            'email' => $dados['email'],
            'password' => Hash::make($dados['password']),
        ]);

        // Após cadastrar, já autentica o usuário.
        Auth::login($usuario);

        // Regenera a sessão após autenticação.
        $request->session()->regenerate();

        return redirect()
            ->route('home')
            ->with('success', 'Conta criada com sucesso.');
    }

    /**
     * Encerra a sessão do usuário.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalida a sessão atual.
        $request->session()->invalidate();

        // Regenera o token CSRF.
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Você saiu da sua conta.');
    }
}
