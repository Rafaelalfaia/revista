<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', config('app.name'))</title>

  @vite(['resources/css/app.css','resources/js/app.js'])
  @stack('head')
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900">
  {{-- Navbar opcional aqui --}}
  <main class="min-h-[100svh]">
    @yield('content')   {{-- <- ESTE É O PONTO CERTO PARA LAYOUT COM @extends --}}
  </main>

  @stack('scripts')
</body>
</html>
