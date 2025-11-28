<section class="trivento-featured-articles py-16 bg-white">
    <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="flex justify-between items-end mb-10 border-b border-gray-200">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 border-b-4 border-pink-600 pb-1">
                Artigos em Destaque
            </h2>
            <a href="{{ route('artigos') }}" class="text-pink-600 hover:text-pink-800 font-semibold flex items-center transition pb-1">Ver todos →</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 sm:gap-10">
            @for ($i = 1; $i <= 3; $i++)
            <div class="article-card border rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition duration-300 bg-white">
                <div class="h-48 bg-gray-200 relative">
                    <img src="https://placehold.co/400x200/db2777/ffffff?text=Capa+Artigo" class="w-full h-full object-cover">

                    <span class="absolute top-3 left-3 bg-pink-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">Categoria Exemplo</span>
                </div>

                <div class="p-6">
                    <h3 class="text-xl font-extrabold text-gray-900 mb-2 hover:text-pink-700 transition cursor-pointer">
                        Aplicações de Machine Learning (Exemplo Artigo {{ $i }})
                    </h3>

                    <p class="text-sm text-gray-500 mb-4">Autor Principal, Coautor 1, Coautor 2</p>

                    <a href="{{ route('artigos') }}" class="text-pink-600 font-semibold flex items-center hover:underline">
                        Ler Artigo
                    </a>
                </div>
            </div>
            @endfor
        </div>

    </div>
</section>


<section class="max-w-7xl mx-auto px-6 mt-32 grid grid-cols-3 gap-6">

    <div class="border rounded-2xl p-8 hover:shadow-lg transition">
        <h3 class="text-xl font-bold mb-2">Para Autores</h3>
        <p class="text-gray-600 mb-4">Diretrizes de submissão e formatação de artigos científicos.</p>
        <a href="#" class="text-pink-600 font-semibold">Acessar →</a>
    </div>

    <div class="border rounded-2xl p-8 hover:shadow-lg transition">
        <h3 class="text-xl font-bold mb-2">Conselho Editorial</h3>
        <p class="text-gray-600 mb-4">Conheça nosso time de editores e revisores.</p>
        <a href="#" class="text-pink-600 font-semibold">Conhecer →</a>
    </div>

    <div class="border rounded-2xl p-8 hover:shadow-lg transition">
        <h3 class="text-xl font-bold mb-2">Sobre a Revista</h3>
        <p class="text-gray-600 mb-4">Missão, valores e compromisso com ciência aberta.</p>
        <a href="#" class="text-pink-600 font-semibold">Saiba mais →</a>
    </div>

</section>
