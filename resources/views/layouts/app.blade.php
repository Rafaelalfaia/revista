<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revista Trivento</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white text-gray-900 antialiased">

    {{-- =============================== --}}
    {{-- CABEÇALHO --}}
    {{-- =============================== --}}

    <header class="w-full bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

            {{-- LOGO --}}
            <a href="{{ route('inicio') }}" class="text-2xl font-serif font-extrabold text-pink-600">
                Revista Trivento
            </a>

            {{-- MENU DESKTOP --}}
            <div class="hidden md:flex items-center gap-6">

                <nav class="flex items-center gap-6 text-gray-700 font-medium">

                    <a href="{{ route('artigos.index') }}"
                       class="hover:text-pink-600 transition {{ request()->routeIs('artigos.index') ? 'text-pink-600 font-semibold' : '' }}">
                        Artigos
                    </a>

                    <a href="{{ route('edicoes') }}"
                       class="hover:text-pink-600 transition {{ request()->routeIs('edicoes') ? 'text-pink-600 font-semibold' : '' }}">
                        Edições
                    </a>

                    <a href="#" class="hover:text-pink-600 transition">
                        Autores
                    </a>

                    <a href="#" class="hover:text-pink-600 transition">
                        Categorias
                    </a>

                    <a href="{{ route('sobre') }}"
                       class="hover:text-pink-600 transition {{ request()->routeIs('sobre') ? 'text-pink-600 font-semibold' : '' }}">
                        Sobre
                    </a>

                </nav>

                {{-- BOTÃO SUBMETER --}}
                <a href="{{ $submissionRoute ?? route('submissao') }}"
                   class="px-4 py-2 rounded-md bg-pink-600 text-white font-semibold shadow hover:bg-pink-700 transition">
                    Submeter Artigo
                </a>

            </div>

            {{-- BOTÃO MOBILE --}}
            <button id="menuButton" class="md:hidden text-gray-700 text-3xl focus:outline-none">
                ☰
            </button>

        </div>

        {{-- MENU MOBILE --}}
        <div id="mobileMenu" class="hidden md:hidden w-full bg-white px-6 pb-6 border-b shadow-sm">

            <a href="{{ route('artigos.index') }}"
               class="block py-2 text-gray-700 hover:text-pink-600 transition {{ request()->routeIs('artigos.index') ? 'text-pink-600 font-semibold' : '' }}">
                Artigos
            </a>

            <a href="{{ route('edicoes') }}"
               class="block py-2 text-gray-700 hover:text-pink-600 transition {{ request()->routeIs('edicoes') ? 'text-pink-600 font-semibold' : '' }}">
                Edições
            </a>

            <a href="#" class="block py-2 text-gray-700 hover:text-pink-600 transition">
                Autores
            </a>

            <a href="#" class="block py-2 text-gray-700 hover:text-pink-600 transition">
                Categorias
            </a>

            <a href="{{ route('sobre') }}"
               class="block py-2 text-gray-700 hover:text-pink-600 transition {{ request()->routeIs('sobre') ? 'text-pink-600 font-semibold' : '' }}">
                Sobre
            </a>

            <a href="{{ $submissionRoute ?? route('submissao') }}"
               class="block mt-4 px-4 py-2 bg-pink-600 text-white font-semibold rounded-md text-center shadow hover:bg-pink-700 transition">
                Submeter Artigo
            </a>

        </div>

    </header>

    {{-- =============================== --}}
    {{-- CONTEÚDO PRINCIPAL --}}
    {{-- =============================== --}}

    <main class="min-h-screen">
        @yield('content')
    </main>

    {{-- =============================== --}}
    {{-- RODAPÉ ATUALIZADO (idêntico à imagem) --}}
    {{-- =============================== --}}

    <footer class="bg-white border-t mt-16">
        <div class="container mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-4 gap-12">

            <!-- COLUNA 1 -->
            <div>
                <h3 class="text-xl font-semibold mb-4">Revista Trivento</h3>
                <p class="text-gray-600 leading-relaxed">
                    Revista científica dedicada à publicação de projetos de excelência em pesquisa e inovação.
                </p>

                <p class="text-gray-500 text-sm mt-4">
                    © 2025 Revista Trivento. ISSN 2674-9876 (online)<br>
                    Licença Creative Commons BY 4.0
                </p>
            </div>

            <!-- COLUNA 2 -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Navegação</h3>
                <ul class="space-y-2 text-gray-700">
                    <li><a href="#" class="hover:text-pink-600">Edições</a></li>
                    <li><a href="#" class="hover:text-pink-600">Artigos</a></li>
                    <li><a href="#" class="hover:text-pink-600">Diretrizes</a></li>
                    <li><a href="#" class="hover:text-pink-600">Categorias</a></li>
                    <li><a href="#" class="hover:text-pink-600">Autores</a></li>
                </ul>
            </div>

            <!-- COLUNA 3 -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Para Autores</h3>
                <ul class="space-y-2 text-gray-700">
                    <li><a href="#" class="hover:text-pink-600">Submeter Artigo</a></li>
                    <li><a href="#" class="hover:text-pink-600">Diretrizes</a></li>
                    <li><a href="#" class="hover:text-pink-600">Editoria</a></li>
                    <li><a href="#" class="hover:text-pink-600">Conselho Editorial</a></li>
                </ul>
            </div>

            <!-- COLUNA 4 -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Newsletter</h3>
                <p class="text-gray-600">Receba atualizações sobre novas edições e chamadas.</p>

                <div class="flex mt-4">
                    <input type="email" placeholder="seu@email.com"
                           class="w-full px-4 py-2 border rounded-l-lg focus:outline-none focus:border-pink-600">

                    <button class="bg-pink-600 text-white px-4 rounded-r-lg hover:bg-pink-700">
                        OK
                    </button>
                </div>

                <!-- ÍCONES -->
                <div class="flex items-center space-x-6 mt-6 text-gray-700 text-2xl">

                    <a href="#" class="hover:text-pink-600">
                        <i class="fab fa-instagram"></i>
                    </a>

                    <a href="#" class="hover:text-pink-600">
                        <i class="fab fa-linkedin"></i>
                    </a>

                    <a href="#" class="hover:text-pink-600">
                        <i class="fab fa-youtube"></i>
                    </a>

                </div>
            </div>

        </div>
    </footer>

    {{-- ÍCONES FONT AWESOME --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>

    {{-- SCRIPT MOBILE --}}
    <script>
        const menuButton = document.getElementById('menuButton');
        const mobileMenu = document.getElementById('mobileMenu');

        menuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>

</body>
</html>
