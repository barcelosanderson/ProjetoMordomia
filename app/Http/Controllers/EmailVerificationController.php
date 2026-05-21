<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmailVerificationController extends Controller
{
    /**
     * Exibe a tela para reenviar o link de verificação.
     */
    public function showNotice()
    {
        return view('auth.verify-email');
    }

    /**
     * Verifica o e-mail do usuário a partir do link enviado.
     */
    public function verify(int $id, string $token)
    {
        $usuario = User::findOrFail($id);

        if ($usuario->email_verified_at) {
            return redirect()
                ->route('login')
                ->with('success', 'Seu e-mail já estava verificado. Faça login normalmente.');
        }

        if (!$usuario->email_verification_token) {
            return redirect()
                ->route('verification.notice')
                ->withErrors([
                    'email' => 'Token de verificação não encontrado. Solicite um novo link.',
                ]);
        }

        if (!Hash::check($token, $usuario->email_verification_token)) {
            return redirect()
                ->route('verification.notice')
                ->withErrors([
                    'email' => 'Link de verificação inválido.',
                ]);
        }

        $usuario->forceFill([
            'email_verified_at' => now(),
            'email_verification_token' => null,
        ])->save();

        return redirect()
            ->route('login')
            ->with('success', 'E-mail verificado com sucesso. Agora você já pode fazer login.');
    }

    /**
     * Reenvia o link de verificação para o e-mail informado.
     */
    public function resend(Request $request)
    {
        $dados = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Informe seu e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'email.exists' => 'Não encontramos uma conta com esse e-mail.',
        ]);

        $usuario = User::where('email', $dados['email'])->firstOrFail();

        if ($usuario->email_verified_at) {
            return redirect()
                ->route('login')
                ->with('success', 'Esse e-mail já está verificado. Faça login normalmente.');
        }

        $token = $this->gerarNovoToken($usuario);

        $this->enviarEmailVerificacao($usuario, $token);

        return back()->with(
            'success',
            'Enviamos um novo link de verificação para o e-mail informado.'
        );
    }

    /**
     * Gera um novo token, salva o hash no banco e retorna o token puro
     * que será enviado no link de verificação.
     */
    private function gerarNovoToken(User $usuario): string
    {
        $token = Str::random(64);

        $usuario->update([
            'email_verification_token' => Hash::make($token),
        ]);

        return $token;
    }

    /**
     * Envia o link de verificação.
     *
     * Em ambiente local com MAIL_MAILER=log, o conteúdo será salvo em:
     * storage/logs/laravel.log
     */
    private function enviarEmailVerificacao(User $usuario, string $token): void
    {
        $link = route('verification.verify', [
            'id' => $usuario->id,
            'token' => $token,
        ]);

        Mail::raw(
            "Olá, {$usuario->name}!\n\n" .
                "Sua conta na MordomIA foi criada.\n\n" .
                "Para ativar seu acesso, verifique seu e-mail clicando no link abaixo:\n\n" .
                "{$link}\n\n" .
                "Se você não criou essa conta, ignore este e-mail.",
            function ($message) use ($usuario) {
                $message->to($usuario->email)
                    ->subject('Verificação de e-mail — MordomIA');
            }
        );
    }
}
