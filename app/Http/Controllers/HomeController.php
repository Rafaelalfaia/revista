<?php

namespace App\Http\Controllers;

use App\Models\Artigo;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Exibe a página inicial com os artigos em destaque.
     */
    public function index()
    {
        // 1. Buscar os artigos no banco de dados e converter diretamente para array.
        // O método toArray() em uma coleção retorna um array de arrays.
        $artigosEmDestaque = Artigo::latest()->take(4)->get()->toArray();

        // 2. Definir as variáveis estáticas (que sua view precisa)
        $editions = [12, 11, 10, 9, 8, 7];
        // O route() procura a rota nomeada 'submeter.projeto'
        $submissionRoute = route('submeter.projeto') ?? '#'; 

        // 3. Passar os dados para a view home.blade.php
        return view('home', [
            'artigos' => $artigosEmDestaque, // Agora é um array pronto para ser usado como $artigo['chave']
            'editions' => $editions,
            'submissionRoute' => $submissionRoute,
        ]);
    }
}