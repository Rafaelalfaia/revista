<?php

namespace App\Http\Controllers;

use App\Models\Artigo;
use Illuminate\Http\Request;

class ArtigoController extends Controller
{
    /**
     * Lista todos os artigos com paginação e filtros.
     */
    public function index(Request $request)
    {
        $query = Artigo::query();

        // Filtro de pesquisa por título ou autores
        if ($request->filled('q')) {
            $query->where('titulo', 'like', "%{$request->q}%")
                  ->orWhere('autores', 'like', "%{$request->q}%");
        }

        // Filtro por categoria
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        // Ordenar por data de publicação e paginar
        $artigos = $query->orderBy('data_publicacao', 'desc')->paginate(6);

        return view('artigos', compact('artigos'));
    }

    /**
     * Mostra um artigo específico.
     */
    public function show($id)
    {
        $artigo = Artigo::findOrFail($id);
        return view('artigo', compact('artigo'));
    }

    /**
     * Mostra o formulário para criar um artigo.
     */
    public function create()
    {
        return view('artigo-create');
    }

    /**
     * Salva um novo artigo no banco.
     */
    public function store(Request $request)
    {
        // Validação dos campos
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'autores' => 'required|string|max:255',
            'categoria' => 'required|string|max:255',
            'data_publicacao' => 'required|date',
            'tempo_leitura' => 'required|string|max:50',
            'imagem' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'arquivo_pdf' => 'nullable|mimes:pdf|max:10240', // PDF opcional
            'conteudo' => 'required|string',
        ]);

        // Upload da imagem
        if ($request->hasFile('imagem')) {
            $data['imagem'] = $request->file('imagem')->store('artigos', 'public');
        }

        // Upload do PDF
        if ($request->hasFile('arquivo_pdf')) {
            $data['arquivo_pdf'] = $request->file('arquivo_pdf')->store('artigos/pdf', 'public');
        }

        Artigo::create($data);

        return redirect()
            ->route('artigos.index')
            ->with('success', 'Artigo criado com sucesso!');
    }
}
