@extends('layouts.app')

@section('title', 'Edições - Revista Trivento')

@section('content')

@php
    $submissionRoute = route('submissao');
    $currentYear = date('Y');
@endphp

<section class="py-16 bg-pink-50">
    <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-serif font-extrabold text-gray-900 mb-4">Todas as Edições</h1>

        <p class="text-lg text-gray-600 mb-12">
            Explore o acervo completo da Revista Trivento.
        </p>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            @for ($i = 12; $i >= 1; $i--)
                <div class="edition-box bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition duration-300 border-t-4 border-pink-500 cursor-pointer">
                    <p class="text-5xl font-black text-pink-600 mb-2">{{ $i }}</p>
                    <h2 class="text-xl font-bold text-gray-900">Edição {{ $i }}</h2>
                    <p class="text-sm text-gray-500 mt-1">Volume 1, {{ 2025 - (12 - $i) }}</p>

                    <!-- CORRIGIDO: rota existente -->
                    <a href="{{ route('artigos.index') }}" class="text-sm text-pink-500 font-semibold mt-4 inline-block hover:underline">
                        Ver Artigos →
                    </a>
                </div>
            @endfor
        </div>

    </div>
</section>

@endsection
