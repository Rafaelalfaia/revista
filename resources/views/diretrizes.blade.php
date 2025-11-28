<?php
// Simula a lógica de rotas do Laravel
$submissionRoute = '/submissao';
$currentYear = date('Y');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revista Trivento - Início</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Georgia', 'Times New Roman', 'serif'],
                    },
                    colors: {
                        'pink-600': '#db2777', 
                        'pink-500': '#ec4899',
                        'pink-100': '#fce7f6',
                        'pink-50': '#fdf2f8',
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white text-gray-800">

<!-- CABEÇALHO COM NAVEGAÇÃO ATIVA PARA 'INÍCIO' -->
<header class="shadow-md bg-white sticky top-0 z-10">
    <div class="container mx-auto max-w-7xl flex items-center justify-between px-4 sm:px-6 lg:px-8 py-4">
        <div class="text-2xl font-extrabold text-pink-600">Revista Trivento</div>
        <nav class="space-x-6 hidden md:flex">
            <!-- Link Início (Home) está ativo com cor e borda -->
            <a href="/" class="text-pink-600 border-b-2 border-pink-600 transition font-semibold">Início</a>
            <a href="/edicoes" class="text-gray-600 hover:text-pink-600 transition font-semibold">Edições</a>
            <a href="/artigos" class="text-gray-600 hover:text-pink-600 transition font-semibold">Artigos</a>
            <!-- O link para Diretrizes está aqui e aponta para /diretrizes -->
            <a href="/diretrizes" class="text-gray-600 hover:text-pink-600 transition font-semibold">Diretrizes</a>
            <a href="/sobre" class="text-gray-600 hover:text-pink-600 transition font-semibold">Sobre</a>
        </nav>
        <a href="<?= $submissionRoute ?>" class="bg-pink-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-pink-700 transition">
            Submissão
        </a>
    </div>
</header>

<!-- CONTEÚDO PRINCIPAL (Exemplo de layout da Home) -->
<main>
    <section class="py-24 bg-pink-50">
        <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-6xl font-serif font-extrabold text-gray-900 mb-4">Bem-vindo à Revista Trivento</h1>
            <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                Publicando projetos de excelência em pesquisa e inovação, fomentando o conhecimento interdisciplinar.
            </p>
            <a href="/edicoes" class="bg-pink-600 text-white px-8 py-4 rounded-xl text-lg font-semibold hover:bg-pink-700 transition transform hover:-translate-y-1 shadow-xl inline-block">
                Explorar Edições Atuais
            </a>
        </div>
    </section>

    <!-- Adicione outros blocos da home page aqui (Destaques, Últimos Artigos, etc.) -->
    <section class="py-16">
        <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Últimos Artigos Publicados</h2>
            <!-- Estrutura de Cards de Artigos aqui -->
        </div>
    </section>
</main>

<!-- RODAPÉ (IDÊNTICO ao das Diretrizes) -->
<footer class="bg-gray-50 border-t border-gray-200 pt-10 pb-8 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8 border-b border-gray-300 pb-10">
            <div class="col-span-2 md:col-span-2 lg:col-span-2">
                <h3 class="font-bold text-lg text-pink-600 mb-4">Trivento</h3>
                <p class="text-sm text-gray-600">
                    Revista científica dedicada à publicação de projetos de excelência em pesquisa e inovação.
                </p>
                <div class="mt-4 text-xs text-gray-500">
                    &copy; <?= $currentYear ?> Revista Trivento. ISSN 2674-9876 (online)
                    <br>
                    Licença Creative Commons BY 4.0
                </div>
            </div>
            <div>
                <h4 class="font-semibold text-gray-700 mb-3">Navegação</h4>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li><a href="/edicoes" class="hover:text-pink-600 transition">Edições</a></li>
                    <li><a href="/artigos" class="hover:text-pink-600 transition">Artigos</a></li>
                    <li><a href="/diretrizes" class="hover:text-pink-600 transition">Diretrizes</a></li>
                    <li><a href="#" class="hover:text-pink-600 transition">Categorias</a></li>
                    <li><a href="#" class="hover:text-pink-600 transition">Autores</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-gray-700 mb-3">Para Autores</h4>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li><a href="<?= $submissionRoute ?>" class="hover:text-pink-600 transition">Submeter Artigo</a></li>
                    <li><a href="/diretrizes" class="hover:text-pink-600 transition">Diretrizes</a></li>
                    <li><a href="#" class="hover:text-pink-600 transition">Editoria</a></li>
                    <li><a href="/sobre" class="hover:text-pink-600 transition">Conselho Editorial</a></li>
                </ul>
            </div>
            <div class="col-span-2 md:col-span-1 lg:col-span-1">
                <h4 class="font-semibold text-gray-700 mb-3">Newsletter</h4>
                <p class="text-sm text-gray-600 mb-3">
                    Receba atualizações sobre novas edições e chamadas.
                </p>
                <form class="flex space-x-2">
                    <input type="email" placeholder="seu@email.com" class="px-3 py-2 border border-gray-300 rounded-md text-sm w-full focus:ring-pink-500 focus:border-pink-500" required>
                    <button type="submit" class="px-4 py-2 bg-pink-600 text-white font-semibold rounded-md hover:bg-pink-700 transition">
                        OK
                    </button>
                </form>
                <div class="mt-6 flex space-x-4 text-gray-500">
                    <a href="https://instagram.com/triventorevista" target="_blank" class="hover:text-pink-600 transition">
                        <svg class="w-6 h-6 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/></svg>
                    </a>
                    <a href="https://linkedin.com/company/triventorevista" target="_blank" class="hover:text-pink-600 transition">
                        <svg class="w-6 h-6 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854zm4.943 12.248V6.169H2.542v7.225zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248S2.4 3.226 2.4 3.934c0 .694.521 1.248 1.327 1.248zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016l.016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225z"/></svg>
                    </a>
                    <a href="https://youtube.com/triventorevista" target="_blank" class="hover:text-pink-600 transition">
                        <svg class="w-6 h-6 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M8.051 1.999h.089c.822.003 4.987.033 6.11.335a2.01 2.01 0 0 1 1.415 1.42c.101.38.172.883.22 1.402l.01.104.022.26.008.104c.065.914.073 1.77.074 1.957v.075c-.001.194-.01 1.108-.082 2.06l-.008.105-.009.104c-.05.572-.124 1.14-.235 1.558a2.01 2.01 0 0 1-1.415 1.42c-1.16.312-5.569.334-6.18.335h-.142c-.309 0-1.587-.006-2.927-.052l-.17-.006-.087-.004-.171-.007-.171-.007c-1.11-.049-2.167-.128-2.654-.26a2.01 2.01 0 0 1-1.415-1.419c-.111-.417-.185-.986-.235-1.558L.09 9.82l-.008-.104A31 31 0 0 1 0 7.68v-.123c.002-.215.01-.958.064-1.778l.007-.103.003-.052.008-.104.022-.26.01-.104c.048-.519.119-1.023.22-1.402a2.01 2.01 0 0 1 1.415-1.42c.487-.13 1.544-.21 2.654-.26l.17-.007.172-.006.086-.003.171-.007A100 100 0 0 1 7.858 2zM6.4 5.209v4.818l4.157-2.408z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>
</body>
</html>