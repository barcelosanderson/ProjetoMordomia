<?php

namespace App\Http\Controllers;

use App\Models\Compromisso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class CompromissoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $compromissos = Compromisso::orderBy('data')->orderBy('hora')->get();
        return view('compromissos.index', compact('compromissos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('compromissos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|min:3|max:100',
            'data'   => 'required|date',
            'hora'   => 'required',
        ]);

        try {
            Compromisso::create($request->all());
        } catch (Exception $e) {
            Log::error('Erro ao inserir compromisso: ' . $e->getMessage(), ['stack' => $e->getStackTraceAsString()]);
        }
        return redirect()->route('compromissos.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $compromisso = Compromisso::findOrFail($id);
        return view('compromissos.edit', compact('compromisso'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'titulo' => 'required|min:3|max:100',
            'data'   => 'required|date',
            'hora'   => 'required',
        ]);

        try {
            $compromisso = Compromisso::findOrFail($id);
            $compromisso->update($request->all());
        } catch (Exception $e) {
            Log::error('Erro ao alterar compromisso: ' . $e->getMessage(), ['stack' => $e->getStackTraceAsString()]);
        }
        return redirect()->route('compromissos.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $compromisso = Compromisso::findOrFail($id);
            $compromisso->delete();
        } catch (Exception $e) {
            Log::error('Erro ao excluir compromisso: ' . $e->getMessage(), ['stack' => $e->getStackTraceAsString()]);
        }
        return redirect()->route('compromissos.index');
    }
}