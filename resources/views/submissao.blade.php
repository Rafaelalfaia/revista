@extends('layouts.app')

@section('title', 'Submissão de Artigo')

@section('content')

{{-- ============================
     HERO / TÍTULO DA PÁGINA
=============================== --}}
<section class="relative bg-[#8C1D40] text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold">Submissão de Artigos</h1>
        <p class="text-lg mt-2 opacity-90">
            Envie seu trabalho para avaliação pela equipe editorial
        </p>
    </div>
</section>


{{-- ============================
     CONTEÚDO PRINCIPAL
=============================== --}}
<section class="container mx-auto px-4 py-12">

    {{-- Breadcrumb --}}
    <nav class="text-sm mb-8">
        <a href="{{ route('inicio') }}" class="text-[#8C1D40] hover:underline">Início</a>
        <span class="text-gray-500 mx-2">/</span>
        <span class="text-gray-700">Submissão</span>
    </nav>

    {{-- Caixa principal --}}
    <div class="bg-white shadow-lg rounded-xl p-8">

        <h2 class="text-2xl font-semibold mb-6">Iniciar Submissão</h2>

        <p class="text-gray-700 leading-relaxed mb-6">
            Utilize o botão abaixo para acessar o ambiente de submissão.  
            Caso ainda não esteja logado, você será direcionado para o login.
        </p>

        {{-- Botão --}}
        @auth
            <a href="{{ route('autor.submissions.create') }}"
               class="inline-block bg-[#8C1D40] text-white px-6 py-3 rounded-lg text-lg font-medium hover:bg-[#A32750] transition">
                📄 Enviar novo artigo
            </a>
        @else
            <a href="{{ route('login') }}"
               class="inline-block bg-[#8C1D40] text-white px-6 py-3 rounded-lg text-lg font-medium hover:bg-[#A32750] transition">
                🔐 Faça login para submeter
            </a>
        @endauth

        <hr class="my-10">

        {{-- Informações adicionais --}}
        <h3 class="text-xl font-semibold mb-4">Diretrizes</h3>

        <p class="text-gray-700 mb-4">
            Antes de enviar, recomendamos que leia atentamente as diretrizes de submissão.
        </p>

        <a href="{{ route('diretrizes') }}"
           class="text-[#8C1D40] font-semibold hover:underline">
            → Ver Diretrizes da Revista
        </a>

    </div>
</section>

@endsection
