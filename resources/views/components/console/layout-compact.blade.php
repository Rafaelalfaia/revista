<!DOCTYPE html>
<html lang="pt-BR" class="antialiased">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>@yield('title','Console · Trivento')</title>

  {{-- cor da barra do navegador (atualizada pelo script) --}}
  <meta name="theme-color" content="#0B0F0D">

  {{-- ⚡ aplica tema antes do CSS para evitar flicker --}}
  <script>
    (() => {
      const KEY = 'trv.theme';
      const saved = localStorage.getItem(KEY) || 'auto';
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      const dark = saved === 'dark' || (saved === 'auto' && prefersDark);
      document.documentElement.classList.toggle('dark', dark);
      const meta = document.querySelector('meta[name="theme-color"]');
      if (meta) meta.setAttribute('content', dark ? '#0B0F0D' : '#ffffff');
      window.__TRV_INITIAL_THEME__ = saved;
    })();
  </script>

  @vite(['resources/css/app.css','resources/js/app.js'])
  @stack('head')
  <style>[x-cloak]{display:none}</style>
</head>
<body
  x-data="themeShell()"
  x-init="init()"
  class="min-h-[100dvh]
         bg-gradient-to-b from-rose-50 to-white
         dark:from-[#0B0F0D] dark:to-[#0B0F0D] text-slate-900 dark:text-slate-100">

  {{-- Acessibilidade --}}
  <a href="#conteudo"
     class="sr-only focus:not-sr-only focus:fixed focus:left-3 focus:top-3 focus:z-50 rounded-md bg-rose-600 px-3 py-2 text-white">
    Ir para o conteúdo
  </a>

  {{-- APP-SHELL centralizado --}}
  <div class="mx-auto my-4 md:my-6 lg:my-8
              w-full md:max-w-[900px] lg:max-w-[1100px]
              min-h-[calc(100dvh-2rem)]
              rounded-[28px] border border-black/5 dark:border-white/10
              bg-white/95 dark:bg-[#0F1412]/95
              shadow-xl ring-1 ring-black/5 dark:ring-white/5
              backdrop-blur supports-[backdrop-filter]:bg-white/80
              dark:supports-[backdrop-filter]:bg-[#0F1412]/80
              overflow-hidden">

    {{-- GRID do app (sidebar + conteúdo) --}}
    <div class="grid min-h-[inherit] lg:grid-cols-[260px_1fr]">

      {{-- SIDEBAR (drawer no mobile; fixa no desktop) --}}
      <aside id="console-sidebar"
             class="fixed inset-y-0 left-0 z-40 w-[86%] max-w-[320px] -translate-x-full
                    bg-white dark:bg-[#0F1412] text-slate-900 dark:text-slate-100
                    border-r border-black/10 dark:border-white/10
                    px-4 py-4 transition-transform duration-200 ease-out
                    lg:static lg:translate-x-0 lg:w-auto lg:h-[inherit] lg:overflow-y-auto"
             :class="menuOpen ? 'translate-x-0' : '-translate-x-full'">

        {{-- Cabeçalho da sidebar --}}
        <div class="flex items-center justify-between gap-2 px-2">
          <div class="flex items-center gap-2">
            <div class="h-3 w-3 rounded-full bg-rose-400"></div>
            <span class="font-semibold">Trivento Console</span>
          </div>
          <button class="lg:hidden inline-flex h-9 w-9 items-center justify-center rounded-lg
                         bg-black/5 hover:bg-black/10 dark:bg-white/5 dark:hover:bg-white/10"
                  @click="menuOpen=false" aria-label="Fechar menu">
            <svg width="20" height="20" fill="none" class="opacity-80">
              <path d="M6 6l8 8M14 6l-8 8" stroke="currentColor" stroke-width="1.5"/>
            </svg>
          </button>
        </div>

        {{-- Navegação (somente Dashboard tem link real) --}}
        <nav class="mt-6 space-y-1 text-sm">
          @php $isDash = request()->routeIs('*dashboard*') || request()->routeIs('dashboard'); @endphp
          <x-console.navlink href="{{ route('dashboard') }}" :active="$isDash">Dashboard</x-console.navlink>

          <div class="mt-4 mb-1 px-2 text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Fluxo Editorial</div>
          <x-console.navlink href="#">Submissões</x-console.navlink>
          <x-console.navlink href="#">Triagem</x-console.navlink>
          <x-console.navlink href="#">Atribuições de Revisão</x-console.navlink>
          <x-console.navlink href="#">Revisões</x-console.navlink>
          <x-console.navlink href="#">Decisões</x-console.navlink>

          <div class="mt-4 mb-1 px-2 text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Publicação</div>
          <x-console.navlink href="#">Edições (número/ano)</x-console.navlink>
          <x-console.navlink href="#">Artigos (pré-publicação)</x-console.navlink>
          <x-console.navlink href="#">DOI & Compliance</x-console.navlink>
          <x-console.navlink href="#">Destaques da Home</x-console.navlink>

          <div class="mt-4 mb-1 px-2 text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Catálogos</div>
          <x-console.navlink href="#">Categorias/Áreas</x-console.navlink>
          <x-console.navlink href="#">Palavras-chave</x-console.navlink>
          <x-console.navlink href="#">Mídias (imagens/PDFs)</x-console.navlink>

          <div class="mt-4 mb-1 px-2 text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Pessoas</div>
          <x-console.navlink href="#">Usuários & Papéis</x-console.navlink>
          <x-console.navlink href="#">Revisores (carga & competências)</x-console.navlink>
          <x-console.navlink href="#">Autores (perfil)</x-console.navlink>

          <div class="mt-4 mb-1 px-2 text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Relatórios & Métricas</div>
          <x-console.navlink href="#">Pipeline por status</x-console.navlink>
          <x-console.navlink href="#">SLA / tempo de decisão</x-console.navlink>
          <x-console.navlink href="#">Taxa de aceitação</x-console.navlink>
          <x-console.navlink href="#">Top artigos / downloads</x-console.navlink>
          <x-console.navlink href="#">Audiência & PWA</x-console.navlink>

          <div class="mt-4 mb-1 px-2 text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Sistema</div>
          <x-console.navlink href="#">Jobs & e-mails</x-console.navlink>
          <x-console.navlink href="#">Erros & Logs</x-console.navlink>
          <x-console.navlink href="#">Armazenamento</x-console.navlink>
          <x-console.navlink href="#">Configurações da Revista</x-console.navlink>
        </nav>

        {{-- Sessão do usuário --}}
        <div class="mt-8 border-t border-black/10 dark:border-white/10 pt-4">
          <div class="text-xs text-slate-500 dark:text-slate-400">Sessão</div>
          @php
            $user = auth()->user();
            $avatar = ($user && !empty($user->avatar_url)) ? $user->avatar_url : asset('images/avatar.png');
          @endphp
          <div class="mt-2 flex items-center gap-3">
            <img src="{{ $avatar }}" class="h-9 w-9 rounded-full object-cover" alt="Foto">
            <div class="leading-tight">
              <div class="text-sm font-medium line-clamp-1">{{ $user?->name }}</div>
              <div class="text-xs text-slate-500 dark:text-slate-400 line-clamp-1">{{ $user?->email }}</div>
            </div>
          </div>
          @if (Route::has('logout'))
            <form method="POST" action="{{ route('logout') }}" class="mt-3">@csrf
              <button type="submit"
                      class="inline-flex text-sm text-slate-700 hover:text-rose-600 dark:text-slate-300 dark:hover:text-rose-300">
                Sair
              </button>
            </form>
          @endif
        </div>
      </aside>

      {{-- BACKDROP do drawer (só mobile) --}}
      <div x-show="menuOpen" x-transition.opacity
           class="fixed inset-0 z-30 bg-black/50 lg:hidden"
           @click="menuOpen=false" aria-hidden="true"></div>

      {{-- MAIN / APP BAR --}}
      <main class="relative p-4 sm:p-6 lg:p-8 overflow-x-hidden">
        <div class="mb-4 flex items-center justify-between rounded-xl
                    border border-black/10 dark:border-white/10
                    bg-white dark:bg-[#12181b] px-3 py-2 sm:px-4 sm:py-3">
          <div class="flex items-center gap-3">
            <button class="lg:hidden inline-flex h-9 w-9 items-center justify-center rounded-lg
                           bg-black/5 hover:bg-black/10 dark:bg-white/5 dark:hover:bg-white/10"
                    @click="menuOpen=true" aria-controls="console-sidebar" aria-label="Abrir menu">
              <svg width="20" height="20" fill="none" class="opacity-80">
                <path d="M3 6h14M3 10h14M3 14h14" stroke="currentColor" stroke-width="1.5"/>
              </svg>
            </button>
            <div class="font-semibold">@yield('page.title','Dashboard')</div>
          </div>

          <div class="flex items-center gap-2">
            {{-- Toggle de tema (menu: auto/claro/escuro) --}}
            <div x-data="{open:false}" class="relative" x-cloak>
              <button @click="open=!open"
                class="inline-flex items-center gap-2 rounded-lg border
                       border-black/10 dark:border-white/10
                       bg-white hover:bg-slate-50 dark:bg-white/5 dark:hover:bg-white/10
                       px-3 py-2 text-sm">
                <template x-if="theme === 'dark'">
                  <svg width="18" height="18" viewBox="0 0 24 24" class="opacity-80"><path fill="currentColor" d="M21 12.79A9 9 0 1 1 11.21 3a7 7 0 1 0 9.79 9.79Z"/></svg>
                </template>
                <template x-if="theme === 'light'">
                  <svg width="18" height="18" viewBox="0 0 24 24" class="opacity-80"><path fill="currentColor" d="M12 18a6 6 0 1 0 0-12a6 6 0 0 0 0 12Z"/></svg>
                </template>
                <template x-if="theme === 'auto'">
                  <svg width="18" height="18" viewBox="0 0 24 24" class="opacity-80"><path fill="currentColor" d="M6 12a6 6 0 0 0 12 0a6 6 0 1 0-12 0z"/></svg>
                </template>
                <span x-text="labelTheme()"></span>
              </button>

              <div x-show="open" @click.outside="open=false"
                   class="absolute right-0 mt-2 w-44 rounded-xl border border-black/10 dark:border-white/10
                          bg-white dark:bg-[#111619] shadow-lg p-2 z-10">
                <button @click="setTheme('auto'); open=false"
                        class="w-full text-left rounded-lg px-3 py-2 text-sm hover:bg-slate-100 dark:hover:bg-white/5"
                        :class="theme==='auto' ? 'text-rose-600 dark:text-rose-300 font-medium' : ''">Automático</button>
                <button @click="setTheme('light'); open=false"
                        class="w-full text-left rounded-lg px-3 py-2 text-sm hover:bg-slate-100 dark:hover:bg-white/5"
                        :class="theme==='light' ? 'text-rose-600 dark:text-rose-300 font-medium' : ''">Modo claro</button>
                <button @click="setTheme('dark'); open=false"
                        class="w-full text-left rounded-lg px-3 py-2 text-sm hover:bg-slate-100 dark:hover:bg-white/5"
                        :class="theme==='dark' ? 'text-rose-600 dark:text-rose-300 font-medium' : ''">Modo escuro</button>
              </div>
            </div>

            <div class="hidden xs:flex items-center gap-2 text-xs sm:text-sm">
              <span class="rounded-full bg-rose-500/10 text-rose-700 dark:text-rose-300 px-2.5 py-1">
                Ambiente: {{ app()->environment() }}
              </span>
              <span class="rounded-full bg-black/5 dark:bg-white/5 px-2.5 py-1">v1.0.0</span>
            </div>
          </div>
        </div>

        {{-- Banner opcional no dashboard --}}
        @if (request()->routeIs('*dashboard*') || request()->routeIs('dashboard'))
          @includeIf('console.partials._banner_principal')
        @endif

        <div id="conteudo" class="pb-[env(safe-area-inset-bottom)]">
          @yield('content')
        </div>
      </main>
    </div>

    {{-- TAB BAR (mobile) fixada no fundo do app-shell --}}
    <nav class="lg:hidden sticky bottom-0 inset-x-0 border-t border-black/10 dark:border-white/10
                bg-white/95 dark:bg-[#0F1412]/95 backdrop-blur supports-[backdrop-filter]:bg-white/80
                dark:supports-[backdrop-filter]:bg-[#0F1412]/80">
      <div class="grid grid-cols-4 text-xs">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center py-2">
          <svg width="20" height="20" fill="none"><path d="M3 11l9-7 9 7v8a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-8Z" stroke="currentColor" stroke-width="1.5"/></svg>
          <span>Home</span>
        </a>
        <a href="#" class="flex flex-col items-center py-2"><svg width="20" height="20" fill="none"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/></svg><span>Filas</span></a>
        <a href="#" class="flex flex-col items-center py-2"><svg width="20" height="20" fill="none"><path d="M4 6h16M4 12h16M4 18h10" stroke="currentColor" stroke-width="1.5"/></svg><span>Relatórios</span></a>
        <button type="button" @click="cycleTheme()" class="flex flex-col items-center py-2">
          <svg width="20" height="20" fill="none"><path d="M12 3a9 9 0 1 0 9 9c0-3.78-2.1-7.06-5.19-8.66A6 6 0 0 1 12 3Z" stroke="currentColor" stroke-width="1.5"/></svg>
          <span>Tema</span>
        </button>
      </div>
    </nav>
  </div>

  <script>
  function themeShell() {
    return {
      menuOpen: JSON.parse(localStorage.getItem('trv.menuOpen') || 'false'),
      theme: window.__TRV_INITIAL_THEME__ || 'auto',
      mql: null,
      init() {
        this.mql = window.matchMedia('(prefers-color-scheme: dark)');
        this.mql.addEventListener?.('change', () => { if (this.theme === 'auto') this.apply(this.theme); });
        this.apply(this.theme);
      },
      labelTheme(){ return this.theme==='auto' ? 'Automático' : (this.theme==='dark' ? 'Modo escuro' : 'Modo claro'); },
      cycleTheme(){ this.setTheme(this.theme==='auto' ? 'light' : this.theme==='light' ? 'dark' : 'auto'); },
      setTheme(t){ this.theme=t; localStorage.setItem('trv.theme', t); this.apply(t); },
      apply(t){
        const prefersDark = this.mql?.matches ?? false;
        const dark = t==='dark' || (t==='auto' && prefersDark);
        document.documentElement.classList.toggle('dark', dark);
        const meta = document.querySelector('meta[name="theme-color"]');
        if (meta) meta.setAttribute('content', dark ? '#0B0F0D' : '#ffffff');
      }
    }
  }
  </script>

  @stack('scripts')
</body>
</html>
