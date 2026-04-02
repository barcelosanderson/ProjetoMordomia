<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Exibe a página inicial com atalhos de navegação.
     */
    public function index()
    {
        return view('home.index');
    }
}