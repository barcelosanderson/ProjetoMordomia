<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class CompraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $compras = Compra::all();
        return view('compras.index', compact('compras'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('compras.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Compra::create($request->all());
        } catch (Exception $e) {
            Log::error('Erro ao inserir item de compra: ' . $e->getMessage(), ['stack' => $e->getStackTraceAsString()]);
        }
        return redirect()->route('compras.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $compra = Compra::findOrFail($id);
        return view('compras.edit', compact('compra'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $compra = Compra::findOrFail($id);
            $compra->update($request->all());
        } catch (Exception $e) {
            Log::error('Erro ao alterar item de compra: ' . $e->getMessage(), ['stack' => $e->getStackTraceAsString()]);
        }
        return redirect()->route('compras.index');
    }

    /**
     * Toggle the purchased status of the specified resource.
     */
    public function toggle($id)
    {
        try {
            $compra = Compra::findOrFail($id);
            $compra->update(['comprado' => !$compra->comprado]);
        } catch (Exception $e) {
            Log::error('Erro ao atualizar status do item: ' . $e->getMessage(), ['stack' => $e->getStackTraceAsString()]);
        }
        return redirect()->route('compras.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $compra = Compra::findOrFail($id);
            $compra->delete();
        } catch (Exception $e) {
            Log::error('Erro ao excluir item de compra: ' . $e->getMessage(), ['stack' => $e->getStackTraceAsString()]);
        }
        return redirect()->route('compras.index');
    }
}