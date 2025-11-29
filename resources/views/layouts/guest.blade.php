<!DOCTYPE html>
{{-- A classe h-full w-full na tag html garante que ela ocupe toda a viewport --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full w-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    
    {{-- MODIFICAÇÃO CHAVE: Adicionado h-full e m-0 (margem zero) no body para remover a rolagem vertical. --}}
    <body class="font-sans text-gray-900 antialiased h-full m-0">
        
        {{-- O container principal mantém min-h-screen para garantir que o $slot preencha toda a tela. --}}
        <div class="min-h-screen w-full"> 
            
            {{-- O conteúdo da sua tela de login/cadastro é injetado aqui. --}}
            {{ $slot }}

        </div>
    </body>
</html>