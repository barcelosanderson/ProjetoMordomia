<?php

namespace App\Http\Controllers;

use App\Models\Tarefa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class TarefaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tarefas = Tarefa::all();
        return view('tarefas.index', compact('tarefas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tarefas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Tarefa::create($request->all());
        } catch (Exception $e) {
            Log::error('Erro ao inserir tarefa: ' . $e->getMessage(), ['stack' => $e->getStackTraceAsString()]);
        }
        return redirect()->route('tarefas.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $tarefa = Tarefa::findOrFail($id);
        return view('tarefas.edit', compact('tarefa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $tarefa = Tarefa::findOrFail($id);
            $tarefa->update($request->all());
        } catch (Exception $e) {
            Log::error('Erro ao alterar tarefa: ' . $e->getMessage(), ['stack' => $e->getStackTraceAsString()]);
        }
        return redirect()->route('tarefas.index');
    }

    /**
     * Toggle the completed status of the specified resource.
     */
    public function toggle($id)
    {
        try {
            $tarefa = Tarefa::findOrFail($id);
            $tarefa->update(['concluida' => !$tarefa->concluida]);
        } catch (Exception $e) {
            Log::error('Erro ao atualizar status da tarefa: ' . $e->getMessage(), ['stack' => $e->getStackTraceAsString()]);
        }
        return redirect()->route('tarefas.index');
    }

    /**
     * Remove the specified resource from storage.
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