<nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

        {{-- LOGO --}}
        <a href="{{ route('inicio') }}" class="text-2xl font-bold text-pink-700">
            Trivento
        </a>

        {{-- MENU PRINCIPAL (DESKTOP) --}}
        <div class="hidden md:flex space-x-8 text-gray-700 font-medium">

            <a href="{{ route('edicoes') }}" class="hover:text-pink-600">Edições</a>

            <a href="{{ route('artigos') }}" class="hover:text-pink-600">Artigos</a>

            <a href="{{ route('autores') }}" class="hover:text-pink-600">Autores</a>

            <a href="{{ route('categorias') }}" class="hover:text-pink-600">Categorias</a>

            <a href="{{ route('conselho.editorial') }}" class="hover:text-pink-600">
                Conselho Editorial
            </a>

            <a href="{{ route('sobre') }}" class="hover:text-pink-600">Sobre</a>
        </div>

        {{-- AÇÕES / LOGIN --}}
        <div class="hidden md:flex items-center space-x-4">

            <a href="{{ route('login') }}" class="text-sm hover:text-pink-600">Login</a>

            <a href="{{ route('register') }}"
               class="px-4 py-2 bg-pink-600 text-white rounded-lg font-semibold hover:bg-pink-700 transition">
               Registrar
            </a>

            <a href="{{ route('submissao') }}"
               class="px-4 py-2 bg-pink-600 text-white rounded-lg font-semibold hover:bg-pink-700 transition">
                Submeter Projeto
            </a>
        </div>

        {{-- BOTÃO MOBILE --}}
        <button id="mobileMenuBtn" class="md:hidden text-pink-700 text-3xl">
            <i class="ph ph-list"></i>
        </button>

    </div>

    {{-- MENU MOBILE --}}
    <div id="mobileMenu" class="hidden md:hidden px-6 pb-6 space-y-4 bg-white border-t border-gray-200">

        <a href="{{ route('edicoes') }}" class="block text-gray-700 hover:text-pink-600">Edições</a>

        <a href="{{ route('artigos') }}" class="block text-gray-700 hover:text-pink-600">Artigos</a>

        <a href="{{ route('autores') }}" class="block text-gray-700 hover:text-pink-600">Autores</a>

        <a href="{{ route('categorias') }}" class="block text-gray-700 hover:text-pink-600">Categorias</a>

        <a href="{{ route('conselho.editorial') }}" class="block text-gray-700 hover:text-pink-600">
            Conselho Editorial
        </a>

        <a href="{{ route('sobre') }}" class="block text-gray-700 hover:text-pink-600">Sobre</a>

        <a href="{{ route('login') }}" class="block text-gray-700 hover:text-pink-600">Login</a>

        <a href="{{ route('register') }}" class="block text-pink-600 font-bold">
            Registrar
        </a>

        <a href="{{ route('submissao') }}" class="block text-pink-600 font-bold">
            Submeter Projeto
        </a>

    </div>
</nav>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById("mobileMenuBtn");
    const menu = document.getElementById("mobileMenu");

    btn.addEventListener("click", () => {
        menu.classList.toggle("hidden");
    });
});
</script>
