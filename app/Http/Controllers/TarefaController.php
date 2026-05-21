<?php

namespace App\Http\Controllers;

use App\Models\Tarefa;
use App\Models\TarefaConclusao;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TarefaController extends Controller
{
    private array $diasSemana = [
        0 => 'Dom',
        1 => 'Seg',
        2 => 'Ter',
        3 => 'Qua',
        4 => 'Qui',
        5 => 'Sex',
        6 => 'Sáb',
    ];

    /**
     * Lista as tarefas do dia da semana selecionado.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();

        $diaHoje = now()->dayOfWeek;

        $diaSelecionado = (int) $request->query('dia', $diaHoje);

        if (!array_key_exists($diaSelecionado, $this->diasSemana)) {
            $diaSelecionado = $diaHoje;
        }

        /*
         * A tela mostra apenas o dia da semana.
         * Internamente usamos a data real da semana atual para permitir reset semanal.
         */
        $inicioSemana = now()->startOfWeek(Carbon::SUNDAY);
        $dataSelecionada = $inicioSemana->copy()
            ->addDays($diaSelecionado)
            ->toDateString();

        $tarefasUsuario = Tarefa::where('user_id', $userId)
            ->latest()
            ->get();

        /*
         * Monta os 7 cards da semana com total de tarefas
         * e quantidade concluída em cada dia.
         */
        $cardsDias = collect($this->diasSemana)
            ->map(function ($nomeDia, $numeroDia) use ($tarefasUsuario, $inicioSemana, $diaHoje, $diaSelecionado, $userId) {
                $dataDia = $inicioSemana->copy()
                    ->addDays($numeroDia)
                    ->toDateString();

                /*
         * Busca as tarefas que pertencem ao dia da semana atual do card.
         */
                $tarefasDoDia = $tarefasUsuario->filter(function ($tarefa) use ($numeroDia) {
                    $diasSemana = array_map('intval', $tarefa->dias_semana ?? []);

                    return in_array((int) $numeroDia, $diasSemana, true);
                });

                $idsTarefasDoDia = $tarefasDoDia
                    ->pluck('id')
                    ->map(fn($id) => (int) $id)
                    ->toArray();

                $total = count($idsTarefasDoDia);

                /*
                * Conta diretamente no banco quantas tarefas daquele dia
                * foram concluídas na data correspondente ao card.
                *
                * Isso evita problema de comparação entre Carbon, string e date.
                */
                $concluidas = 0;

                if ($total > 0) {
                    $concluidas = TarefaConclusao::where('user_id', $userId)
                        ->whereDate('data', $dataDia)
                        ->whereIn('tarefa_id', $idsTarefasDoDia)
                        ->count();
                }

                return [
                    'numero' => $numeroDia,
                    'nome' => $nomeDia,
                    'data' => $dataDia,
                    'total' => $total,
                    'concluidas' => $concluidas,
                    'pendentes' => max($total - $concluidas, 0),
                    'percentual' => $total > 0 ? round(($concluidas / $total) * 100) : 0,
                    'hoje' => (int) $numeroDia === (int) $diaHoje,
                    'selecionado' => (int) $numeroDia === (int) $diaSelecionado,
                ];
            })
            ->values();

        $conclusoesSelecionadas = TarefaConclusao::where('user_id', $userId)
            ->whereDate('data', $dataSelecionada)
            ->pluck('tarefa_id')
            ->map(fn($id) => (int) $id)
            ->toArray();

        $tarefas = $tarefasUsuario
            ->filter(function ($tarefa) use ($diaSelecionado) {
                $diasSemana = array_map('intval', $tarefa->dias_semana ?? []);

                return in_array((int) $diaSelecionado, $diasSemana, true);
            })
            ->map(function ($tarefa) use ($conclusoesSelecionadas) {
                /*
         * Criamos uma propriedade temporária para a tela.
         * Ela não é salva na tabela tarefas.
         */
                $tarefa->concluida_no_dia = in_array((int) $tarefa->id, $conclusoesSelecionadas, true);

                return $tarefa;
            })
            ->values();

        return view('tarefas.index', compact(
            'tarefas',
            'cardsDias',
            'diaSelecionado',
            'dataSelecionada'
        ));
    }

    /**
     * Abre o formulário de criação.
     */
    public function create()
    {
        return view('tarefas.create', [
            'diasSemana' => $this->diasSemana,
        ]);
    }

    /**
     * Salva uma nova tarefa vinculada ao usuário logado.
     */
    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|min:3|max:100',
            'dias_semana' => 'required|array|min:1',
            'dias_semana.*' => 'integer|between:0,6',
        ], [
            'nome.required' => 'O nome da tarefa é obrigatório.',
            'nome.min' => 'O nome da tarefa deve ter pelo menos 3 caracteres.',
            'nome.max' => 'O nome da tarefa não pode ultrapassar 100 caracteres.',

            'dias_semana.required' => 'Selecione pelo menos um dia da semana.',
            'dias_semana.array' => 'Selecione dias válidos para a tarefa.',
            'dias_semana.min' => 'Selecione pelo menos um dia da semana.',
            'dias_semana.*.integer' => 'Dia da semana inválido.',
            'dias_semana.*.between' => 'Dia da semana inválido.',
        ]);

        try {
            Tarefa::create([
                'user_id' => auth()->id(),
                'nome' => $dados['nome'],
                'concluida' => false,
                'dias_semana' => array_map('intval', $dados['dias_semana']),
            ]);

            return redirect()
                ->route('tarefas.index')
                ->with('success', 'Tarefa criada com sucesso.');
        } catch (Exception $e) {
            Log::error('Erro ao inserir tarefa: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Não foi possível criar a tarefa.');
        }
    }

    /**
     * Abre o formulário de edição somente se a tarefa pertencer ao usuário logado.
     */
    public function edit($id)
    {
        $tarefa = Tarefa::where('user_id', auth()->id())
            ->findOrFail($id);

        return view('tarefas.edit', [
            'tarefa' => $tarefa,
            'diasSemana' => $this->diasSemana,
        ]);
    }

    /**
     * Atualiza somente tarefas do usuário logado.
     */
    public function update(Request $request, $id)
    {
        $dados = $request->validate([
            'nome' => 'required|min:3|max:100',
            'dias_semana' => 'required|array|min:1',
            'dias_semana.*' => 'integer|between:0,6',
        ], [
            'nome.required' => 'O nome da tarefa é obrigatório.',
            'nome.min' => 'O nome da tarefa deve ter pelo menos 3 caracteres.',
            'nome.max' => 'O nome da tarefa não pode ultrapassar 100 caracteres.',

            'dias_semana.required' => 'Selecione pelo menos um dia da semana.',
            'dias_semana.array' => 'Selecione dias válidos para a tarefa.',
            'dias_semana.min' => 'Selecione pelo menos um dia da semana.',
            'dias_semana.*.integer' => 'Dia da semana inválido.',
            'dias_semana.*.between' => 'Dia da semana inválido.',
        ]);

        try {
            $tarefa = Tarefa::where('user_id', auth()->id())
                ->findOrFail($id);

            $tarefa->update([
                'nome' => $dados['nome'],
                'dias_semana' => array_map('intval', $dados['dias_semana']),
            ]);

            return redirect()
                ->route('tarefas.index')
                ->with('success', 'Tarefa atualizada com sucesso.');
        } catch (Exception $e) {
            Log::error('Erro ao alterar tarefa: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Não foi possível atualizar a tarefa.');
        }
    }

    /**
     * Marca ou desmarca uma tarefa como concluída em uma data específica.
     */
    public function toggle(Request $request, $id)
    {
        $dados = $request->validate([
            'data_referencia' => 'required|date',
            'dia_semana' => 'required|integer|between:0,6',
        ]);

        try {
            $userId = auth()->id();

            $tarefa = Tarefa::where('user_id', $userId)
                ->findOrFail($id);

            $dataReferencia = Carbon::parse($dados['data_referencia'])->toDateString();
            $diaSemana = Carbon::parse($dataReferencia)->dayOfWeek;

            /*
         * Garante que a tarefa realmente pertence ao dia selecionado.
         */
            if (!in_array($diaSemana, $tarefa->dias_semana ?? [])) {
                return redirect()
                    ->route('tarefas.index', ['dia' => $dados['dia_semana']])
                    ->with('error', 'Essa tarefa não pertence ao dia selecionado.');
            }

            /*
         * Procura se já existe conclusão dessa tarefa nessa data.
         */
            $conclusao = TarefaConclusao::where('user_id', $userId)
                ->where('tarefa_id', $tarefa->id)
                ->whereDate('data', $dataReferencia)
                ->first();

            /*
         * Se já existe, remove.
         * Se não existe, cria.
         */
            if ($conclusao) {
                $conclusao->delete();
            } else {
                TarefaConclusao::create([
                    'user_id' => $userId,
                    'tarefa_id' => $tarefa->id,
                    'data' => $dataReferencia,
                ]);
            }

            return redirect()
                ->route('tarefas.index', ['dia' => $dados['dia_semana']]);
        } catch (Exception $e) {
            Log::error('Erro ao atualizar status da tarefa: ' . $e->getMessage());

            return redirect()
                ->route('tarefas.index', ['dia' => $request->input('dia_semana', now()->dayOfWeek)])
                ->with('error', 'Não foi possível alterar o status da tarefa.');
        }
    }

    /**
     * Exclui somente tarefas do usuário logado.
     */
    public function destroy($id)
    {
        try {
            $tarefa = Tarefa::where('user_id', auth()->id())
                ->findOrFail($id);

            $tarefa->delete();

            return redirect()
                ->route('tarefas.index')
                ->with('success', 'Tarefa excluída com sucesso.');
        } catch (Exception $e) {
            Log::error('Erro ao excluir tarefa: ' . $e->getMessage());

            return back()->with('error', 'Não foi possível excluir a tarefa.');
        }
    }
}
