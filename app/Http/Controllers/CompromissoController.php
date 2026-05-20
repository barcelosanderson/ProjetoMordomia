<?php

namespace App\Http\Controllers;

use App\Models\Compromisso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class CompromissoController extends Controller
{
    /**
     * Lista somente os compromissos do usuário logado.
     */
    public function index()
    {
        $compromissos = Compromisso::where('user_id', auth()->id())
            ->orderBy('data')
            ->orderBy('hora')
            ->get();

        return view('compromissos.index', compact('compromissos'));
    }

    /**
     * Abre formulário de criação.
     */
    public function create()
    {
        return view('compromissos.create');
    }

    /**
     * Salva compromisso vinculado ao usuário logado.
     */
    public function store(Request $request)
    {
        $dados = $request->validate([
            'titulo' => 'required|min:3|max:100',
            'data'   => 'required|date',
            'hora'   => 'required',
        ], [
            'titulo.required' => 'O título do compromisso é obrigatório.',
            'titulo.min'      => 'O título deve ter pelo menos 3 caracteres.',
            'titulo.max'      => 'O título não pode ultrapassar 100 caracteres.',
            'data.required'   => 'A data do compromisso é obrigatória.',
            'data.date'       => 'A data informada não é válida.',
            'hora.required'   => 'A hora do compromisso é obrigatória.',
        ]);

        try {
            Compromisso::create([
                'user_id' => auth()->id(),
                'titulo' => $dados['titulo'],
                'data' => $dados['data'],
                'hora' => $dados['hora'],
            ]);

            return redirect()
                ->route('compromissos.index')
                ->with('success', 'Compromisso criado com sucesso.');
        } catch (Exception $e) {
            Log::error('Erro ao inserir compromisso: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Não foi possível criar o compromisso.');
        }
    }

    /**
     * Edita somente compromissos do usuário logado.
     */
    public function edit($id)
    {
        $compromisso = Compromisso::where('user_id', auth()->id())
            ->findOrFail($id);

        return view('compromissos.edit', compact('compromisso'));
    }

    /**
     * Atualiza somente compromissos do usuário logado.
     */
    public function update(Request $request, $id)
    {
        $dados = $request->validate([
            'titulo' => 'required|min:3|max:100',
            'data'   => 'required|date',
            'hora'   => 'required',
        ], [
            'titulo.required' => 'O título do compromisso é obrigatório.',
            'titulo.min'      => 'O título deve ter pelo menos 3 caracteres.',
            'titulo.max'      => 'O título não pode ultrapassar 100 caracteres.',
            'data.required'   => 'A data do compromisso é obrigatória.',
            'data.date'       => 'A data informada não é válida.',
            'hora.required'   => 'A hora do compromisso é obrigatória.',
        ]);

        try {
            $compromisso = Compromisso::where('user_id', auth()->id())
                ->findOrFail($id);

            $compromisso->update([
                'titulo' => $dados['titulo'],
                'data' => $dados['data'],
                'hora' => $dados['hora'],
            ]);

            return redirect()
                ->route('compromissos.index')
                ->with('success', 'Compromisso atualizado com sucesso.');
        } catch (Exception $e) {
            Log::error('Erro ao alterar compromisso: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Não foi possível atualizar o compromisso.');
        }
    }

    /**
     * Exclui somente compromissos do usuário logado.
     */
    public function destroy($id)
    {
        try {
            $compromisso = Compromisso::where('user_id', auth()->id())
                ->findOrFail($id);

            $compromisso->delete();

            return redirect()
                ->route('compromissos.index')
                ->with('success', 'Compromisso excluído com sucesso.');
        } catch (Exception $e) {
            Log::error('Erro ao excluir compromisso: ' . $e->getMessage());

            return back()->with('error', 'Não foi possível excluir o compromisso.');
        }
    }
}
