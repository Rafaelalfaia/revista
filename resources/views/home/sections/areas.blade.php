{{-- resources/views/home/sections/areas.blade.php --}}

@php
    $cards = [
        [
            'title' => 'Para Autores',
            'desc'  => 'Diretrizes de submissão, formatação e fluxo editorial — tudo o que você precisa para publicar conosco.',
            'route' => route('diretrizes'),
            'cta'   => 'Diretrizes',
            'icon'  => 'ph-file-text'
        ],
        [
            'title' => 'Conselho Editorial',
            'desc'  => 'Conheça os editores e revisores responsáveis pela qualidade científica da revista.',
            'route' => route('conselho.editorial'),
            'cta'   => 'Nossos Membros',
            'icon'  => 'ph-user-gear'
        ],
        [
            'title' => 'Sobre a Revista',
            'desc'  => 'Missão, escopo e nosso compromisso com acesso aberto e boas práticas editoriais.',
            'route' => route('sobre'),
            'cta'   => 'Saiba Mais',
            'icon'  => 'ph-book'
        ],
    ];
@endphp

<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Conheça Nossas Áreas</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            @foreach($cards as $c)
                <a href="{{ $c['route'] }}" class="group block p-8 rounded-xl border border-gray-200 shadow-sm hover:shadow-lg hover:-translate-y-1 transition bg-white">

                    <div class="flex items-start space-x-4">

                        <div class="w-14 h-14 rounded-lg bg-pink-50 flex items-center justify-center">
                            <i class="ph {{ $c['icon'] }} text-pink-600 text-3xl"></i>
                        </div>

                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-pink-700">
                                {{ $c['title'] }}
                            </h3>

                            <p class="text-gray-600 mb-4">
                                {{ $c['desc'] }}
                            </p>

                            <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-pink-50 text-pink-700">
                                {{ $c['cta'] }}
                            </span>
                        </div>

                    </div>

                </a>
            @endforeach

        </div>

    </div>
</section>
