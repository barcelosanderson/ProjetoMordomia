<?php

namespace App\Http\Controllers;

use App\Models\Transacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class TransacaoController extends Controller
{
    /**
     * Lista somente as transações do usuário logado
     * e calcula os totais apenas com os dados dele.
     */
    public function index()
    {
        $transacoes = Transacao::where('user_id', auth()->id())
            ->latest()
            ->get();

        $totalReceitas = Transacao::where('user_id', auth()->id())
            ->where('tipo', 'receita')
            ->sum('valor');

        $totalDespesas = Transacao::where('user_id', auth()->id())
            ->where('tipo', 'despesa')
            ->sum('valor');

        $saldo = $totalReceitas - $totalDespesas;

        return view('financas.index', compact(
            'transacoes',
            'totalReceitas',
            'totalDespesas',
            'saldo'
        ));
    }

    /**
     * Abre formulário de criação.
     */
    public function create()
    {
        return view('financas.create');
    }

    /**
     * Salva transação vinculada ao usuário logado.
     */
    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome'  => 'required|min:3|max:100',
            'valor' => 'required|numeric|min:0.01',
            'tipo'  => 'required|in:receita,despesa',
        ], [
            'nome.required'  => 'A descrição da transação é obrigatória.',
            'nome.min'       => 'A descrição deve ter pelo menos 3 caracteres.',
            'nome.max'       => 'A descrição não pode ultrapassar 100 caracteres.',
            'valor.required' => 'O valor da transação é obrigatório.',
            'valor.numeric'  => 'O valor informado não é válido.',
            'valor.min'      => 'O valor deve ser maior que zero.',
            'tipo.required'  => 'Selecione o tipo da transação.',
            'tipo.in'        => 'O tipo selecionado não é válido.',
        ]);

        try {
            Transacao::create([
                'user_id' => auth()->id(),
                'nome' => $dados['nome'],
                'valor' => $dados['valor'],
                'tipo' => $dados['tipo'],
            ]);

            return redirect()
                ->route('financas.index')
                ->with('success', 'Transação criada com sucesso.');
        } catch (Exception $e) {
            Log::error('Erro ao inserir transação: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Não foi possível criar a transação.');
        }
    }

    /**
     * Exclui somente transações do usuário logado.
     */
    public function destroy($id)
    {
        try {
            $transacao = Transacao::where('user_id', auth()->id())
                ->findOrFail($id);

            $transacao->delete();

            return redirect()
                ->route('financas.index')
                ->with('success', 'Transação excluída com sucesso.');
        } catch (Exception $e) {
            Log::error('Erro ao excluir transação: ' . $e->getMessage());

            return back()->with('error', 'Não foi possível excluir a transação.');
        }
    }
}
