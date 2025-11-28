@extends('layouts.app')

@section('title', 'Criar Artigo - Revista Trivento')

@section('content')
<div class="max-w-3xl mx-auto py-10">

    <h1 class="text-3xl font-bold mb-6 text-gray-900">Criar Novo Artigo</h1>

    <form action="{{ route('artigos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label class="block font-semibold mb-2">Título</label>
            <input type="text" name="titulo" class="w-full border rounded p-2" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-2">Autores</label>
            <input type="text" name="autores" class="w-full border rounded p-2" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-2">Categoria</label>
            <input type="text" name="categoria" class="w-full border rounded p-2" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-2">Data de Publicação</label>
            <input type="date" name="data_publicacao" class="w-full border rounded p-2" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-2">Tempo de Leitura (ex: 12 min)</label>
            <input type="text" name="tempo_leitura" class="w-full border rounded p-2" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-2">Imagem do Artigo</label>
            <input type="file" name="imagem" accept="image/*" class="w-full border rounded p-2 bg-white" required>
        </div>

        <div class="mb-4">
    <label class="block font-semibold mb-2">Arquivo PDF (opcional)</label>
    <input type="file" name="arquivo_pdf" accept="application/pdf"
           class="w-full border rounded p-2 bg-white">
</div>






        <div class="mb-4">
            <label class="block font-semibold mb-2">Conteúdo</label>
            <textarea name="conteudo" rows="6" class="w-full border rounded p-2" required></textarea>
        </div>

        <button class="bg-pink-600 text-white px-6 py-3 rounded shadow hover:bg-pink-700 transition">
            Salvar Artigo
        </button>
    </form>

</div>
@endsection
