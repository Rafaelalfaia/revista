@extends('layouts.app') 
{{-- A view está usando o seu layout principal, 'layouts.app' --}}

@section('content')
<div class="container mx-auto max-w-4xl py-12 px-4 sm:px-6 lg:px-8">
    
    @if (isset($artigo))
        <article class="bg-white p-8 rounded-xl shadow-lg">
            
            {{-- Título do Artigo --}}
            <h1 class="text-4xl font-extrabold text-gray-900 mb-4">{{ $artigo['titulo'] }}</h1>
            
            {{-- Metadados: Autores, Data e Categoria --}}
            <div class="text-gray-600 mb-6 border-b pb-4">
                <p class="mb-1">
                    <span class="font-semibold text-pink-600">Autores:</span> {{ $artigo['autores'] }}
                </p>
                <p>
                    <span class="font-semibold text-pink-600">Publicado em:</span> {{ $artigo['data'] }} (Tempo de leitura: {{ $artigo['tempo'] }})
                </p>
                {{-- Categoria como um rótulo --}}
                <span class="inline-block mt-2 bg-pink-100 text-pink-800 text-sm font-medium px-3 py-1 rounded-full">{{ $artigo['categoria'] }}</span>
            </div>

           {{-- 💡 AGORA EXIBINDO O CONTEÚDO REAL DO ARTIGO BUSCADO DO BANCO --}}
<div class="prose max-w-none text-gray-800 mb-10">
    <p>
        {{ $artigo['conteudo'] }}
    </p>
</div>
            
            {{-- Link para voltar para a Home --}}
            <a href="{{ route('inicio') }}" class="mt-8 inline-block text-pink-600 hover:text-pink-800 font-semibold transition duration-150">
                &larr; Voltar para a Home
            </a>

        </article>
    @else
        <p class="text-center text-xl text-red-500">Artigo não encontrado.</p>
    @endif
</div>
@endsection