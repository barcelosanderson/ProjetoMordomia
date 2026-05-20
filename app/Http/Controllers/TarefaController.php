<?php

namespace App\Http\Controllers;

use App\Models\Tarefa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class TarefaController extends Controller
{
    /**
     * Lista somente as tarefas do usuário logado.
     */
    public function index()
    {
        $tarefas = Tarefa::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('tarefas.index', compact('tarefas'));
    }

    /**
     * Abre o formulário de criação.
     */
    public function create()
    {
        return view('tarefas.create');
    }

    /**
     * Salva uma nova tarefa vinculada ao usuário logado.
     */
    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|min:3|max:100',
        ], [
            'nome.required' => 'O nome da tarefa é obrigatório.',
            'nome.min'      => 'O nome da tarefa deve ter pelo menos 3 caracteres.',
            'nome.max'      => 'O nome da tarefa não pode ultrapassar 100 caracteres.',
        ]);

        try {
            Tarefa::create([
                'user_id' => auth()->id(),
                'nome' => $dados['nome'],
                'concluida' => false,
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

        return view('tarefas.edit', compact('tarefa'));
    }

    /**
     * Atualiza somente tarefas do usuário logado.
     */
    public function update(Request $request, $id)
    {
        $dados = $request->validate([
            'nome' => 'required|min:3|max:100',
        ], [
            'nome.required' => 'O nome da tarefa é obrigatório.',
            'nome.min'      => 'O nome da tarefa deve ter pelo menos 3 caracteres.',
            'nome.max'      => 'O nome da tarefa não pode ultrapassar 100 caracteres.',
        ]);

        try {
            $tarefa = Tarefa::where('user_id', auth()->id())
                ->findOrFail($id);

            $tarefa->update([
                'nome' => $dados['nome'],
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
     * Inverte o status da tarefa somente se ela pertencer ao usuário logado.
     */
    public function toggle($id)
    {
        try {
            $tarefa = Tarefa::where('user_id', auth()->id())
                ->findOrFail($id);

            $tarefa->update([
                'concluida' => !$tarefa->concluida,
            ]);

            return redirect()->route('tarefas.index');
        } catch (Exception $e) {
            Log::error('Erro ao atualizar status da tarefa: ' . $e->getMessage());

            return back()->with('error', 'Não foi possível alterar o status da tarefa.');
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
