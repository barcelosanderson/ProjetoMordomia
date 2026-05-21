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
    /**
     * Envia a mensagem para a IA, interpreta o retorno
     * e cria o registro correspondente para o usuário logado.
     */
    public function enviar(Request $request)
    {
        $request->validate([
            'mensagem' => 'required|string|max:2000',
        ], [
            'mensagem.required' => 'Digite uma mensagem para a MordomIA.',
            'mensagem.max' => 'A mensagem não pode ultrapassar 2000 caracteres.',
        ]);

        $mensagem = $request->input('mensagem');
        $apiKey   = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return back()->with('erro_chat', 'A chave da API Gemini não foi configurada.');
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

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

        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($url, [
                    'contents' => [
                        [
                            'role'  => 'user',
                            'parts' => [
                                ['text' => $systemPrompt . "\n\nComando do usuário: " . $mensagem],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => 512,
                        'temperature'     => 0.1,
                    ],
                ]);
        } catch (\Exception $e) {
            Log::error('Erro de conexão com Gemini: ' . $e->getMessage());

            return back()->with('erro_chat', 'Não foi possível conectar à IA. Tente novamente.');
        }

        if ($response->failed()) {
            Log::error('Gemini erro: ' . $response->status() . ' - ' . $response->body());

            return back()->with('erro_chat', 'A IA não respondeu corretamente. Tente novamente.');
        }

        $raw = $response->json('candidates.0.content.parts.0.text');

        if (!$raw) {
            return back()->with('erro_chat', 'A resposta da IA veio vazia. Tente novamente.');
        }

        // Remove possíveis marcações markdown do JSON.
        $raw = preg_replace('/```json|```/', '', $raw);
        $raw = trim($raw);

        $resultado = json_decode($raw, true);

        if (!$resultado || !isset($resultado['acao'])) {
            Log::warning('Resposta inválida da IA: ' . $raw);

            return back()->with('erro_chat', 'Não consegui interpretar sua mensagem. Tente novamente.');
        }

        $confirmacao = $resultado['confirmacao'] ?? 'Feito! 🤵‍♂️';
        $dados = $resultado['dados'] ?? [];

        try {
            switch ($resultado['acao']) {
                case 'criar_compromisso':
                    if (
                        empty($dados['titulo']) ||
                        empty($dados['data']) ||
                        empty($dados['hora'])
                    ) {
                        return back()->with('erro_chat', 'Faltaram dados para criar o compromisso.');
                    }

                    Compromisso::create([
                        'user_id' => auth()->id(),
                        'titulo' => $dados['titulo'],
                        'data'   => $dados['data'],
                        'hora'   => $dados['hora'],
                    ]);
                    break;

                case 'criar_tarefa':
                    if (empty($dados['nome'])) {
                        return back()->with('erro_chat', 'Faltou o nome da tarefa.');
                    }

                    Tarefa::create([
                        'user_id' => auth()->id(),
                        'nome' => $dados['nome'],
                        'concluida' => false,

                        /*
                        * Quando a tarefa é criada pelo chat, por enquanto ela será diária.
                        * Depois podemos evoluir o prompt da IA para interpretar dias específicos.
                        */
                        'dias_semana' => [0, 1, 2, 3, 4, 5, 6],
                    ]);
                    break;

                case 'criar_transacao':
                    if (
                        empty($dados['nome']) ||
                        !isset($dados['valor']) ||
                        empty($dados['tipo'])
                    ) {
                        return back()->with('erro_chat', 'Faltaram dados para criar a transação.');
                    }

                    if (!in_array($dados['tipo'], ['receita', 'despesa'])) {
                        return back()->with('erro_chat', 'Tipo de transação inválido.');
                    }

                    Transacao::create([
                        'user_id' => auth()->id(),
                        'nome'  => $dados['nome'],
                        'valor' => (float) $dados['valor'],
                        'tipo'  => $dados['tipo'],
                    ]);
                    break;

                case 'criar_compra':
                    if (empty($dados['nome'])) {
                        return back()->with('erro_chat', 'Faltou o nome do item de compra.');
                    }

                    Compra::create([
                        'user_id' => auth()->id(),
                        'nome' => $dados['nome'],
                        'comprado' => false,
                    ]);
                    break;

                case 'nao_entendido':
                    break;

                default:
                    return back()->with('erro_chat', 'Ação não reconhecida pela MordomIA.');
            }
        } catch (\Exception $e) {
            Log::error('Erro ao criar registro pelo chat: ' . $e->getMessage());

            return back()->with('erro_chat', 'Entendi sua mensagem, mas não consegui salvar o registro.');
        }

        // Salva o histórico da conversa apenas na sessão do usuário atual.
        $historico = session('chat_historico', []);
        $historico[] = [
            'pergunta' => $mensagem,
            'resposta' => $confirmacao,
        ];

        session(['chat_historico' => $historico]);

        return redirect()->route('home');
    }

    /**
     * Limpa o histórico do chat salvo na sessão.
     */
    public function limpar()
    {
        session()->forget('chat_historico');

        return redirect()->route('home');
    }
}
