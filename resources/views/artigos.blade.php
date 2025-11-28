@extends('layouts.app')

@section('title', 'Artigos - Revista Trivento')

@section('content')
<section class="py-16 bg-white">
    <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <h1 class="text-4xl font-serif font-extrabold text-gray-900 mb-4">
            Pesquisa de Artigos
        </h1>
        <p class="text-lg text-gray-600 mb-8">
            Encontre trabalhos científicos por título, autor, categoria ou palavra-chave.
        </p>

        {{-- Barra de Pesquisa --}}
        <form method="GET" action="{{ route('artigos.index') }}" class="flex flex-col md:flex-row gap-4 mb-12">
            <input 
                type="search" 
                name="q" 
                value="{{ request('q') }}" 
                placeholder="Buscar por título ou autor..." 
                class="flex-grow p-3 border border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500"
            >

            <select 
                name="categoria" 
                class="p-3 border border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500"
            >
                <option value="">Todas as Áreas</option>
                <option value="Ciências Sociais" {{ request('categoria') == 'Ciências Sociais' ? 'selected' : '' }}>Ciências Sociais</option>
                <option value="Tecnologia e Inovação" {{ request('categoria') == 'Tecnologia e Inovação' ? 'selected' : '' }}>Tecnologia e Inovação</option>
                <option value="Educação" {{ request('categoria') == 'Educação' ? 'selected' : '' }}>Educação</option>
            </select>

            <button class="bg-pink-600 text-white p-3 rounded-lg font-semibold hover:bg-pink-700 transition">
                Buscar
            </button>
        </form>

        {{-- Grid de Artigos --}}
        @if($artigos->isEmpty())
            <p class="text-center text-gray-500 py-12">Nenhum artigo encontrado.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($artigos as $artigo)
                    <a href="{{ route('artigo.show', $artigo->id) }}"
                       class="article-card rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition duration-300 bg-gray-50/70 border border-gray-100">

                        {{-- Imagem da Capa --}}
                        <div class="h-48 bg-gray-900 relative overflow-hidden">
                            @if($artigo->imagem)
                                <img src="{{ asset('storage/' . $artigo->imagem) }}"
                                     alt="Capa do artigo {{ $artigo->titulo }}"
                                     class="w-full h-full object-cover">
                            @else
                                <img src="{{ asset('images/artigos/default.jpg') }}"
                                     alt="Capa padrão"
                                     class="w-full h-full object-cover">
                            @endif

                            <span class="absolute top-3 left-3 bg-pink-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md z-10">
                                {{ $artigo->categoria }}
                            </span>
                        </div>

                        {{-- Conteúdo --}}
                        <div class="p-6">
                            <h3 class="text-xl font-extrabold text-gray-900 mb-2 leading-snug hover:text-pink-700 transition">
                                {{ $artigo->titulo }}
                            </h3>

                            <p class="text-sm text-gray-600 mb-3">
                                {{ $artigo->autores }} | 
                                {{ $artigo->data_publicacao ? \Carbon\Carbon::parse($artigo->data_publicacao)->format('d/m/Y') : '' }}
                            </p>

                            <p class="text-gray-700 text-sm mb-4 line-clamp-3">
                                {{ \Illuminate\Support\Str::limit(strip_tags($artigo->conteudo), 140) }}
                            </p>

                            {{-- PDF --}}
                            @if (!empty($artigo->arquivo_pdf))
                                <div class="mb-2">
                                    <a href="{{ asset('storage/' . $artigo->arquivo_pdf) }}" target="_blank" 
                                       class="text-pink-600 font-semibold hover:underline text-sm">
                                        Download PDF
                                    </a>
                                </div>
                            @endif

                            <div class="flex items-center justify-between">
                                <span class="text-pink-600 font-semibold hover:underline">Ler Artigo &rarr;</span>
                                <span class="text-xs text-gray-400">{{ $artigo->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Paginação --}}
            @if(method_exists($artigos, 'links'))
                <div class="mt-10">
                    {{ $artigos->links('pagination::tailwind') }}
                </div>
            @endif
        @endif

    </div>
</section>
@endsection
