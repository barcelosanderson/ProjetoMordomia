<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class CompraController extends Controller
{
    /**
     * Lista somente os itens de compra do usuário logado.
     */
    public function index()
    {
        $compras = Compra::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('compras.index', compact('compras'));
    }

    /**
     * Abre formulário de criação.
     */
    public function create()
    {
        return view('compras.create');
    }

    /**
     * Salva item de compra vinculado ao usuário logado.
     */
    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|min:3|max:100',
        ], [
            'nome.required' => 'O nome do item é obrigatório.',
            'nome.min'      => 'O nome do item deve ter pelo menos 3 caracteres.',
            'nome.max'      => 'O nome do item não pode ultrapassar 100 caracteres.',
        ]);

        try {
            Compra::create([
                'user_id' => auth()->id(),
                'nome' => $dados['nome'],
                'comprado' => false,
            ]);

            return redirect()
                ->route('compras.index')
                ->with('success', 'Item criado com sucesso.');
        } catch (Exception $e) {
            Log::error('Erro ao inserir item de compra: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Não foi possível criar o item.');
        }
    }

    /**
     * Edita somente itens do usuário logado.
     */
    public function edit($id)
    {
        $compra = Compra::where('user_id', auth()->id())
            ->findOrFail($id);

        return view('compras.edit', compact('compra'));
    }

    /**
     * Atualiza somente itens do usuário logado.
     */
    public function update(Request $request, $id)
    {
        $dados = $request->validate([
            'nome' => 'required|min:3|max:100',
        ], [
            'nome.required' => 'O nome do item é obrigatório.',
            'nome.min'      => 'O nome do item deve ter pelo menos 3 caracteres.',
            'nome.max'      => 'O nome do item não pode ultrapassar 100 caracteres.',
        ]);

        try {
            $compra = Compra::where('user_id', auth()->id())
                ->findOrFail($id);

            $compra->update([
                'nome' => $dados['nome'],
            ]);

            return redirect()
                ->route('compras.index')
                ->with('success', 'Item atualizado com sucesso.');
        } catch (Exception $e) {
            Log::error('Erro ao alterar item de compra: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Não foi possível atualizar o item.');
        }
    }

    /**
     * Inverte o status comprado/não comprado.
     */
    public function toggle($id)
    {
        try {
            $compra = Compra::where('user_id', auth()->id())
                ->findOrFail($id);

            $compra->update([
                'comprado' => !$compra->comprado,
            ]);

            return redirect()->route('compras.index');
        } catch (Exception $e) {
            Log::error('Erro ao atualizar status do item: ' . $e->getMessage());

            return back()->with('error', 'Não foi possível alterar o status do item.');
        }
    }

    /**
     * Exclui somente itens do usuário logado.
     */
    public function destroy($id)
    {
        try {
            $compra = Compra::where('user_id', auth()->id())
                ->findOrFail($id);

            $compra->delete();

            return redirect()
                ->route('compras.index')
                ->with('success', 'Item excluído com sucesso.');
        } catch (Exception $e) {
            Log::error('Erro ao excluir item de compra: ' . $e->getMessage());

            return back()->with('error', 'Não foi possível excluir o item.');
        }
    }
}
