<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class PasswordResetController extends Controller
{
    /**
     * Exibe a tela onde o usuário informa o e-mail
     * para receber o link de redefinição de senha.
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Gera o token de redefinição e envia o link por e-mail.
     *
     * Em ambiente local, como o .env está com MAIL_MAILER=log,
     * o e-mail será salvo no arquivo storage/logs/laravel.log.
     */
    public function sendResetLink(Request $request)
    {
        $dados = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Informe seu e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'email.exists' => 'Não encontramos uma conta com esse e-mail.',
        ]);

        $email = $dados['email'];

        // Token puro enviado no link.
        $token = Str::random(64);

        // Salva o token criptografado no banco.
        // Assim, mesmo que alguém veja o banco, não consegue usar o token diretamente.
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        $link = route('password.reset', [
            'token' => $token,
            'email' => $email,
        ]);

        // Envio simples de e-mail.
        // Depois podemos trocar por uma classe Mailable mais bonita, se quiser.
        Mail::raw(
            "Olá!\n\nRecebemos uma solicitação para redefinir sua senha na MordomIA.\n\nAcesse o link abaixo para criar uma nova senha:\n\n{$link}\n\nSe você não solicitou essa alteração, ignore este e-mail.",
            function ($message) use ($email) {
                $message->to($email)
                    ->subject('Redefinição de senha — MordomIA');
            }
        );

        return back()->with(
            'success',
            'Enviamos um link de redefinição para o e-mail informado.'
        );
    }

    /**
     * Exibe a tela de nova senha.
     */
    public function showResetPassword(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Valida o token e atualiza a senha do usuário.
     */
    public function resetPassword(Request $request)
    {
        $dados = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'token' => ['required', 'string'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->numbers(),
            ],
        ], [
            'email.required' => 'Informe seu e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'email.exists' => 'Não encontramos uma conta com esse e-mail.',

            'token.required' => 'Token inválido.',

            'password.required' => 'Informe a nova senha.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.letters' => 'A senha deve conter pelo menos uma letra.',
            'password.numbers' => 'A senha deve conter pelo menos um número.',
        ]);

        $registroToken = DB::table('password_reset_tokens')
            ->where('email', $dados['email'])
            ->first();

        if (!$registroToken) {
            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'Solicitação de redefinição não encontrada.',
                ]);
        }

        // Tokens antigos deixam de ser válidos após 60 minutos.
        $tokenExpirado = now()->diffInMinutes($registroToken->created_at) > 60;

        if ($tokenExpirado) {
            DB::table('password_reset_tokens')
                ->where('email', $dados['email'])
                ->delete();

            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'O link de redefinição expirou. Solicite um novo.',
                ]);
        }

        // Compara o token recebido no link com o hash salvo no banco.
        if (!Hash::check($dados['token'], $registroToken->token)) {
            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'Token de redefinição inválido.',
                ]);
        }

        $usuario = User::where('email', $dados['email'])->firstOrFail();

        $usuario->update([
            'password' => Hash::make($dados['password']),
        ]);

        // Remove o token após o uso para impedir reutilização.
        DB::table('password_reset_tokens')
            ->where('email', $dados['email'])
            ->delete();

        return redirect()
            ->route('login')
            ->with('success', 'Senha redefinida com sucesso. Faça login com sua nova senha.');
    }
}
