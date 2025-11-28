<header class="w-full bg-white shadow-sm border-b border-gray-100">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">

        <!-- LOGO -->
        <a href="{{ route('inicio') }}" class="text-2xl font-bold text-pink-600">
            Trivento
        </a>

        <!-- MENU -->
        <nav class="hidden md:flex space-x-8 text-gray-700 font-medium">
            <a href="{{ route('edicoes') }}" class="hover:text-pink-600">Edições</a>
            <a href="{{ route('artigos') }}" class="hover:text-pink-600">Artigos</a>
            <a href="{{ route('autores') }}" class="hover:text-pink-600">Autores</a>
            <a href="{{ route('categorias') }}" class="hover:text-pink-600">Categorias</a>
            <a href="{{ route('inicio') }}" class="hover:text-pink-600">Sobre</a>
        </nav>

        <!-- AÇÕES -->
        <div class="flex items-center space-x-4">

            <!-- Icone busca -->
            <button class="text-gray-600 hover:text-pink-600 text-lg">
                <i class="bi bi-search"></i>
            </button>

            <!-- Botão -->
            <a href="{{ route('submissao') }}"
               class="bg-pink-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-pink-700 shadow">
                Submeter Projeto
            </a>

        </div>

    </div>
</header>
