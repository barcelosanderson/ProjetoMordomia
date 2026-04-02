<?php

namespace App\Http\Controllers;

use App\Models\Transacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class TransacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transacoes = Transacao::all();
        $totalReceitas = Transacao::where('tipo', 'receita')->sum('valor');
        $totalDespesas = Transacao::where('tipo', 'despesa')->sum('valor');
        $saldo = $totalReceitas - $totalDespesas;
        return view('financas.index', compact('transacoes', 'totalReceitas', 'totalDespesas', 'saldo'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('financas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Transacao::create($request->all());
        } catch (Exception $e) {
            Log::error('Erro ao inserir transação: ' . $e->getMessage(), ['stack' => $e->getStackTraceAsString()]);
        }
        return redirect()->route('financas.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $transacao = Transacao::findOrFail($id);
            $transacao->delete();
        } catch (Exception $e) {
            Log::error('Erro ao excluir transação: ' . $e->getMessage(), ['stack' => $e->getStackTraceAsString()]);
        }
        return redirect()->route('financas.index');
    }
}