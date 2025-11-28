@extends('layouts.app')

@section('content')

<main>
    {{-- 🛑 REMOVIDO: O bloco @php estático foi removido.
        As variáveis $submissionRoute, $editions e $artigos
        agora são injetadas diretamente pelo HomeController.php. --}}

    {{-- 🦸 HERO SECTION (Seção principal) --}}
    <section class="trivento-hero bg-gray-50 py-20">
        <div class="container mx-auto max-w-7xl flex flex-col md:flex-row items-center justify-between px-4 sm:px-6 lg:px-8">
            {{-- Conteúdo Principal --}}
            <div class="hero-content w-full md:w-1/2 md:pr-12 mb-10 md:mb-0">
                <span class="edition-tag text-sm font-semibold text-pink-600 border border-pink-600 px-3 py-1 rounded-full mb-4 inline-block tracking-widest">
                    EDIÇÃO 12 - 2025
                </span>

                <h1 class="text-5xl sm:text-6xl font-serif font-extrabold text-gray-900 mb-4 leading-tight">Revista Trivento</h1>

                <p class="text-lg sm:text-xl text-gray-600 mb-8">
                    Publicação científica de acesso aberto dedicada à excelência em pesquisa e inovação. 
                </p>

                <div class="hero-actions flex flex-wrap gap-4">
                    <a href="#" class="btn-primary flex items-center bg-pink-600 text-white px-8 py-3 rounded-xl shadow-lg hover:bg-pink-700 transition duration-300 transform hover:scale-105">
                        Ler Edição Atual &rarr;
                    </a>
                    <a href="{{ $submissionRoute }}" class="btn-secondary border-2 border-pink-600 text-pink-600 px-8 py-3 rounded-xl hover:bg-pink-100 transition duration-300">
                        Submeter Projeto
                    </a>
                </div>
            </div>

            {{-- Card de Destaque da Edição --}}
            <div class="edition-card-container w-full md:w-1/2 flex justify-center md:justify-end relative">
                {{-- Usa a classe .edition-card do app.css --}}
                <div class="edition-card text-white p-8 w-64 sm:w-80 h-80 sm:h-96 rounded-3xl shadow-2xl relative overflow-hidden transform rotate-2 hover:rotate-0 transition-transform duration-500">
                    <span class="status-tag absolute top-0 right-0 bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-bl-lg">
                        Atual
                    </span>
                    <div class="flex flex-col justify-end h-full">
                        <p class="text-7xl sm:text-8xl font-black mb-2 leading-none">12</p>
                        <p class="text-xl sm:text-2xl font-semibold">Edição 2025</p>
                        <p class="text-sm opacity-80 mt-1">ISSN 1234-5678</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Estatísticas --}}
        <div class="container mx-auto max-w-7xl mt-16 flex flex-wrap justify-around text-center stats-bar border-t border-b border-gray-200 py-6 px-4 sm:px-6 lg:px-8">
            {{-- ... (Estrutura de estatísticas) ... --}}
            <div class="stat-item px-4 sm:px-8 py-2 w-1/3 sm:w-auto">
                <p class="text-3xl sm:text-4xl font-extrabold text-pink-600">6</p>
                <p class="text-gray-500 text-xs sm:text-sm">Edições Publicadas</p>
            </div>
            <div class="stat-item px-4 sm:px-8 py-2 w-1/3 sm:w-auto">
                <p class="text-3xl sm:text-4xl font-extrabold text-pink-600">60+</p>
                <p class="text-gray-500 text-xs sm:text-sm">Artigos Aprovados</p>
            </div>
            <div class="stat-item px-4 sm:px-8 py-2 w-1/3 sm:w-auto">
                <p class="text-3xl sm:text-4xl font-extrabold text-pink-600">24h</p>
                <p class="text-gray-500 text-xs sm:text-sm">Tempo Médio de 1ª Revisão</p>
            </div>
        </div>
    </section>

    {{-- 📢 CHAMADA PARA ARTIGOS --}}
    <section class="trivento-call-for-papers bg-white py-12">
        <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="bg-pink-50 p-6 sm:p-8 rounded-2xl shadow-lg flex flex-col lg:flex-row justify-between items-center transform hover:scale-[1.005] transition-transform duration-300">
                <div class="w-full lg:w-3/4 mb-4 lg:mb-0">
                    <h2 class="text-lg sm:text-xl font-bold text-pink-700 mb-1 flex items-start">
                        <span class="mr-3 text-2xl sm:text-3xl flex-shrink-0">📢</span> Chamada Aberta: Edição Especial - Inteligência Artificial e Sociedade
                    </h2>
                    <p class="text-gray-600 text-sm sm:text-md pl-10">
                        Convidamos pesquisadores a submeterem artigos originais. <span class="font-semibold">Prazo Final: 30 de Junho de 2025</span>
                    </p>
                </div>
                <a href="{{ $submissionRoute }}" class="btn-secondary bg-white border-2 border-pink-600 text-pink-600 px-6 py-3 rounded-xl hover:bg-pink-100 transition flex items-center whitespace-nowrap w-full lg:w-auto justify-center">
                    Submeta Seu Artigo &rarr;
                </a>
            </div>
        </div>
    </section>

    {{-- 💎 ARTIGOS EM DESTAQUE --}}
<section class="trivento-featured-articles py-16 bg-white">
    <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-10 border-b border-gray-200">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 border-b-4 border-pink-600 pb-1">Artigos em Destaque</h2>
            <a href="#" class="text-pink-600 hover:text-pink-800 font-semibold flex items-center transition pb-1">
                Ver todos <span class="ml-2">&rarr;</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 sm:gap-10">
            @foreach ($artigos as $artigo)
                <a href="{{ route('artigo.show', ['id' => $artigo['id']]) }}" 
                   class="article-card rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition duration-300 bg-gray-50/70 border border-gray-100">
                    
                    {{-- CAPA --}}
                    <div class="h-48 bg-gray-900 relative overflow-hidden">
                        <img src="{{ asset('images/artigos/' . ($artigo['imagem'] ?? 'default.jpg')) }}" 
                            alt="Capa do artigo sobre {{ $artigo['categoria'] ?? 'Artigo' }}" 
                            class="w-full h-full object-cover">
                        
                        <span class="absolute top-3 left-3 bg-pink-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md z-10">
                            {{ $artigo['categoria'] }}
                        </span>
                    </div>

                    {{-- CONTEÚDO --}}
                    <div class="p-6">
                        <h3 class="text-xl font-extrabold text-gray-900 mb-3 leading-snug hover:text-pink-700 transition cursor-pointer">
                            {{ $artigo['titulo'] }}
                        </h3>

                        {{-- Autores --}}
                        <p class="text-sm text-gray-600 mb-2 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            {{ $artigo['autores'] }}
                        </p>

                        {{-- Data e tempo corrigidos --}}
                        <div class="text-sm text-gray-500 mb-4 flex items-center space-x-4">
                            <span class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ \Carbon\Carbon::parse($artigo['data_publicacao'])->format('d/m/Y') }}
                            </span>

                            <span class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $artigo['tempo_leitura'] }}
                            </span>
                        </div>

                        {{-- Link --}}
                        <span class="text-pink-600 font-semibold flex items-center hover:underline mt-2">
                            Ler Artigo
                        </span>
                    </div>
                </a>
            @endforeach
           </div>
    </div>
</section>


    {{-- 📚 EDIÇÕES ANTERIORES --}}
    <section class="trivento-past-editions py-16 bg-gray-50">
        <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-10 border-b border-gray-200">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 border-b-4 border-pink-600 pb-1">Edições Anteriores</h2>
                <a href="#" class="text-pink-600 hover:text-pink-800 font-semibold flex items-center transition pb-1">
                    Ver todas <span class="ml-2">&rarr;</span>
                </a>
            </div>

            <div class="flex space-x-4 sm:space-x-6 overflow-x-auto pb-4 custom-scrollbar">
                @foreach ($editions as $edition)
                    <div class="edition-item flex-shrink-0 text-center p-4 border-2 border-gray-200 rounded-xl shadow-lg w-28 sm:w-32 hover:border-pink-500 hover:shadow-xl transition duration-300 cursor-pointer bg-white">
                        <p class="text-4xl sm:text-5xl font-black text-gray-800 mb-1">{{ $edition }}</p>
                        <p class="text-sm sm:text-md font-semibold text-pink-600">Edição</p>
                        <p class="text-xs sm:text-sm text-gray-500">2024</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 🔑 ÁREAS DE CONHECIMENTO & INFO CHAVE --}}
    <section class="trivento-areas py-16 bg-white">
        <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 text-center mb-10">Conheça Nossas Áreas</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 sm:gap-10 text-left">
                
                {{-- Box 1: Para Autores (SVG Restaurado) --}}
                <div class="info-box p-8 border-t-4 border-pink-600 rounded-lg shadow-xl bg-white hover:bg-pink-50 transition duration-300">
                    <span class="text-pink-600 mb-4 inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-10 h-10">
                            <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                            <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                            <path d="M10 9H8"/>
                            <path d="M16 13H8"/>
                            <path d="M16 17H8"/>
                        </svg>
                    </span>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Para Autores</h3>
                    <p class="text-gray-600 mb-4">Diretrizes para submissão, formatação de artigos e fluxo editorial.</p>
                    <a href="#" class="text-pink-600 font-semibold flex items-center hover:underline">Diretrizes &rarr;</a>
                </div>

                {{-- Box 2: Conselho Editorial (SVG Restaurado) --}}
                <div class="info-box p-8 border-t-4 border-pink-600 rounded-lg shadow-xl bg-white hover:bg-pink-50 transition duration-300">
                    <span class="text-pink-600 mb-4 inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-10 h-10">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </span>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Conselho Editorial</h3>
                    <p class="text-gray-600 mb-4">Conheça nosso time de editores e revisores especializados por área.</p>
                    <a href="#" class="text-pink-600 font-semibold flex items-center hover:underline">Nossos Membros &rarr;</a>
                </div>

                {{-- Box 3: Sobre a Revista (SVG Restaurado) --}}
                <div class="info-box p-8 border-t-4 border-pink-600 rounded-lg shadow-xl bg-white hover:bg-pink-50 transition duration-300">
                    <span class="text-pink-600 mb-4 inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-10 h-10">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                        </svg>
                    </span>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Sobre a Revista</h3>
                    <p class="text-gray-600 mb-4">Missão, escopo e nosso compromisso com a ciência de acesso aberto (Open Access).</p>
                    <a href="#" class="text-pink-600 font-semibold flex items-center hover:underline">Saiba Mais &rarr;</a>
                </div>
            </div>
        </div>
    </section>

</main>
@endsection