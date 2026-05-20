<?php

namespace App\Http\Controllers;

use App\Models\Tarefa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class TarefaController extends Controller
{
    /**
     * Display a listing of the resource. Lista todas as tarefas
     */
    public function index()
    {
        $tarefas = Tarefa::all();
        return view('tarefas.index', compact('tarefas'));
    }

    /**
     * Show the form for creating a new resource. Só abre o formulário, não acessa bd
     */
    public function create()
    {
        return view('tarefas.create');
    }

    /**
     * Store a newly created resource in storage. Salva nova tarefa
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|min:3|max:100',
        ], [
            'nome.required' => 'O nome da tarefa é obrigatório.',
            'nome.min'      => 'O nome da tarefa deve ter pelo menos 3 caracteres.',
            'nome.max'      => 'O nome da tarefa não pode ultrapassar 100 caracteres.',
        ]);

        try {
            Tarefa::create($request->all()); //pega tudo e cria no banco
        } catch (Exception $e) {
            Log::error('Erro ao inserir tarefa: ' . $e->getMessage(), ['stack' => $e->getStackTraceAsString()]);
        }
        return redirect()->route('tarefas.index'); //redireciona a listagem
    }

    /**
     * Show the form for editing the specified resource. Abre formulário preenchido
     */
    public function edit($id)
    {
        $tarefa = Tarefa::findOrFail($id); // Busca pelo ID
        return view('tarefas.edit', compact('tarefa'));
    }

    /**
     * Update the specified resource in storage. Salva alterações
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|min:3|max:100',
        ], [
            'nome.required' => 'O nome da tarefa é obrigatório.',
            'nome.min'      => 'O nome da tarefa deve ter pelo menos 3 caracteres.',
            'nome.max'      => 'O nome da tarefa não pode ultrapassar 100 caracteres.',
        ]);

        try {
            $tarefa = Tarefa::findOrFail($id); // busca o registro
            $tarefa->update($request->all()); // atualiza com os novos dados
        } catch (Exception $e) {
            Log::error('Erro ao alterar tarefa: ' . $e->getMessage(), ['stack' => $e->getStackTraceAsString()]);
        }
        return redirect()->route('tarefas.index');
    }

    /**
     * Toggle the completed status of the specified resource. Inverte o status 
     */
    public function toggle($id)
    {
        try {
            $tarefa = Tarefa::findOrFail($id);
            $tarefa->update(['concluida' => !$tarefa->concluida]); // ! inverte, se é true vira false
        } catch (Exception $e) {
            Log::error('Erro ao atualizar status da tarefa: ' . $e->getMessage(), ['stack' => $e->getStackTraceAsString()]);
        }
        return redirect()->route('tarefas.index');
    }

    /**
     * Remove the specified resource from storage. Exclui
     */
    public function destroy($id)
    {
        try {
            $tarefa = Tarefa::findOrFail($id);
            $tarefa->delete();
        } catch (Exception $e) {
            Log::error('Erro ao excluir tarefa: ' . $e->getMessage(), ['stack' => $e->getStackTraceAsString()]);
        }
        return redirect()->route('tarefas.index');
    }
}

// todo método usa try/catch pois se der erro, ele registra no log e não quebra a aplicação