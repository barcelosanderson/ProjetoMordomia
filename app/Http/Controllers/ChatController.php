<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Tarefa;
use App\Models\Compromisso;
use App\Models\Transacao;
use App\Models\Compra;

class ChatController extends Controller
{
    public function enviar(Request $request)
    {
        $request->validate([
            'mensagem' => 'required|string|max:2000',
        ]);

        $mensagem = $request->input('mensagem');
        $apiKey   = env('GEMINI_API_KEY');
        $url      = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        $hoje = now()->format('d/m/Y');

        $systemPrompt = <<<PROMPT
Você é a MordomIA, um mordomo pessoal digital extremamente eficiente e discreto.
Hoje é {$hoje}.

Sua ÚNICA função é interpretar comandos do usuário e retornar um JSON estruturado.
Você NÃO responde perguntas gerais, NÃO dá conselhos, NÃO faz conversas.
Você APENAS interpreta intenções e retorna JSON.

## AÇÕES DISPONÍVEIS

### Compromisso
{"acao":"criar_compromisso","dados":{"titulo":"string","data":"YYYY-MM-DD","hora":"HH:MM"},"confirmacao":"mensagem amigável para o usuário 🤵‍♂️"}

### Tarefa
{"acao":"criar_tarefa","dados":{"nome":"string"},"confirmacao":"mensagem amigável para o usuário 🤵‍♂️"}

### Transação financeira
{"acao":"criar_transacao","dados":{"nome":"string","valor":0.00,"tipo":"receita|despesa"},"confirmacao":"mensagem amigável para o usuário 🤵‍♂️"}

### Item de compra
{"acao":"criar_compra","dados":{"nome":"string"},"confirmacao":"mensagem amigável para o usuário 🤵‍♂️"}

### Não entendeu
{"acao":"nao_entendido","confirmacao":"Não entendi o que deseja fazer. Pode reformular? 🤵‍♂️"}

## REGRAS
- Retorne APENAS o JSON, sem texto antes ou depois, sem markdown, sem explicações.
- Datas relativas: "amanhã" = dia seguinte a hoje, "semana que vem" = próxima segunda-feira.
- Se não tiver hora, use "00:00".
- Se não tiver valor em transação, use 0.00.
- O campo "confirmacao" deve ser uma frase curta e simpática confirmando a ação, sempre terminando com 🤵‍♂️.
- Palavras como "pagar", "recebi", "ganhei", "gastei", "comprei" (no sentido financeiro) indicam transação.
- Palavras como "comprar", "precisar de", "falta" indicam item de compra.
- Palavras como "dentista", "reunião", "consulta", "hora marcada" com data/hora indicam compromisso.
- Qualquer outra coisa sem data é tarefa.
PROMPT;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, [
            'contents' => [
                [
                    'role'  => 'user',
                    'parts' => [['text' => $systemPrompt . "\n\nComando do usuário: " . $mensagem]]
                ]
            ],
            'generationConfig' => [
                'maxOutputTokens' => 512,
                'temperature'     => 0.1,
            ]
        ]);

        if ($response->failed()) {
            Log::error('Gemini erro: ' . $response->status() . ' - ' . $response->body());
            return back()->with('erro_chat', 'Não foi possível conectar à IA. Tente novamente.');
        }

        $raw = $response->json('candidates.0.content.parts.0.text');

        // Remove possíveis marcações markdown do JSON
        $raw = preg_replace('/```json|```/', '', $raw);
        $raw = trim($raw);

        $resultado = json_decode($raw, true);

        if (!$resultado || !isset($resultado['acao'])) {
            return back()->with('erro_chat', 'Não consegui interpretar sua mensagem. Tente novamente.');
        }

        $confirmacao = $resultado['confirmacao'] ?? 'Feito! 🎩';
        $dados       = $resultado['dados'] ?? [];

        switch ($resultado['acao']) {
            case 'criar_compromisso':
                Compromisso::create([
                    'titulo' => $dados['titulo'],
                    'data'   => $dados['data'],
                    'hora'   => $dados['hora'],
                ]);
                break;

            case 'criar_tarefa':
                Tarefa::create([
                    'nome' => $dados['nome'],
                ]);
                break;

            case 'criar_transacao':
                Transacao::create([
                    'nome'  => $dados['nome'],
                    'valor' => $dados['valor'],
                    'tipo'  => $dados['tipo'],
                ]);
                break;

            case 'criar_compra':
                Compra::create([
                    'nome' => $dados['nome'],
                ]);
                break;
        }

        // Salva no histórico da sessão
        $historico   = session('chat_historico', []);
        $historico[] = ['pergunta' => $mensagem, 'resposta' => $confirmacao];
        session(['chat_historico' => $historico]);

        return redirect()->route('home');
    }

    public function limpar()
    {
        session()->forget('chat_historico');
        return redirect()->route('home');
    }
}